<?php

namespace App\Http\Controllers;

use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use App\Models\LoanSchedule;
use App\Models\LoanSurveyPhoto;
use App\Models\User;
use App\Support\Notify;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Survey scheduling, stage 2 of the credit flow. The section head (Kepala Seksi Analis) sets a survey date
 * and surveyor for a submitted file. Every schedule, reschedule and cancellation is written to
 * `loan_schedules` and never deleted.
 */
class SchedulingController extends Controller
{
    private const PER_PAGE_OPTIONS = [10, 25, 50];

    private const SORTABLE = ['application_code', 'application_date', 'full_name', 'survey_date', 'status'];

    /**
     * `survey` files stay listed: a survey judged insufficient needs a RE-SURVEY by a more senior role.
     *
     * @return list<LoanStatus>
     */
    private static function openStatuses(): array
    {
        return [LoanStatus::Submitted, LoanStatus::Scheduling, LoanStatus::Survey];
    }

    public function index(Request $request): Response
    {
        $open = array_map(fn (LoanStatus $s): string => $s->value, self::openStatuses());
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', Rule::in([...$open, 'all'])], 'scope' => ['nullable', 'in:mine,all'],
            'sort' => ['nullable', 'string'], 'direction' => ['nullable', 'in:asc,desc'], 'per_page' => ['nullable', 'integer'],
        ]);
        $scope = $filters['scope'] ?? 'mine';
        // By default the list shows what still has to be scheduled; 'all' lists every open file.
        $status = $filters['status'] ?? LoanStatus::Submitted->value;
        $sort = in_array($filters['sort'] ?? null, self::SORTABLE, true) ? $filters['sort'] : 'application_date';
        $direction = $filters['direction'] ?? 'desc';

        $loans = LoanApplication::query()
            ->with(['product:id,alias,name', 'office:id,alias', 'supervisor:id,name', 'surveyor:id,name', 'schedules'])
            ->withCount('surveys')
            ->whereIn('status', $open)
            ->when($scope === 'mine', fn ($q) => $q->where('supervisor_id', $request->user()->id))
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->when($filters['search'] ?? null, function ($q, string $term) {
                $like = '%'.addcslashes($term, '%_\\').'%';
                $q->where(fn ($w) => $w->where('application_code', 'like', $like)->orWhere('full_name', 'like', $like)->orWhere('nik', 'like', $like));
            })
            // Unless a sort was asked for, files the surveyor sent back come first: they have waited the longest.
            ->when(! isset($filters['sort']), fn ($q) => $q->orderByRaw('(loan_applications.status = ? and exists (select 1 from loan_schedules s where s.loan_application_id = loan_applications.id and s.action = ?)) desc', [LoanStatus::Submitted->value, LoanSchedule::ACTION_CANCEL]))
            ->orderBy($sort, $direction)->orderBy('id')
            ->paginate(in_array((int) ($filters['per_page'] ?? 0), self::PER_PAGE_OPTIONS, true) ? (int) $filters['per_page'] : 10)
            ->withQueryString();

        return Inertia::render('scheduling/index', [
            'loans' => $loans->through(fn (LoanApplication $l): array => $this->row($l)),
            'filters' => ['search' => $filters['search'] ?? '', 'status' => $status, 'scope' => $scope, 'sort' => $sort, 'direction' => $direction, 'per_page' => $loans->perPage()],
            'perPageOptions' => self::PER_PAGE_OPTIONS,
            'statuses' => [
                ['value' => LoanStatus::Submitted->value, 'label' => 'Not scheduled yet'],
                ['value' => LoanStatus::Scheduling->value, 'label' => 'Scheduled'],
                ['value' => LoanStatus::Survey->value, 'label' => 'Surveyed'],
                ['value' => 'all', 'label' => 'All statuses'],
            ],
            'maxSchedules' => (int) config('credit.max_schedules'),
            'canManage' => $request->user()->can('scheduling.manage'),
            'canCancel' => $request->user()->can('surveys.manage'),
        ]);
    }

    /** Set or repeat the survey schedule. */
    public function store(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        if (! in_array($loanApplication->status, self::openStatuses(), true)) {
            return back()->with('error', 'This file is not at the scheduling stage.');
        }

        $done = $this->scheduleCount($loanApplication);
        $allowed = array_column($this->surveyors($loanApplication), 'value');

        $data = $request->validate([
            'survey_date' => ['required', 'date', 'after_or_equal:today'],
            'surveyor_id' => ['required', 'integer', Rule::in($allowed)],
            'note' => ['nullable', 'string', 'max:255'],
        ], ['surveyor_id.in' => 'This surveyor is not allowed at this stage.'], ['survey_date' => 'survey date', 'surveyor_id' => 'surveyor']);

        $surveyor = User::query()->whereKey($data['surveyor_id'])->firstOrFail();
        $walkIn = $this->isWalkIn($loanApplication);
        $resurvey = $loanApplication->status === LoanStatus::Survey;

        LoanSchedule::create([
            'loan_application_id' => $loanApplication->id,
            'sequence' => $done + 1,
            'action' => match (true) {
                $resurvey => LoanSchedule::ACTION_RESURVEY,
                $done === 0 => LoanSchedule::ACTION_SCHEDULE,
                default => LoanSchedule::ACTION_RESCHEDULE,
            },
            'survey_date' => $data['survey_date'],
            'surveyor_id' => $surveyor->id,
            'surveyor_name' => $surveyor->name,
            'note' => $data['note'] ?? null,
            'created_by' => $request->user()->name,
        ]);

        // Walk-in product: no field survey, so the file goes straight to analysis.
        $loanApplication->update([
            'status' => $walkIn ? LoanStatus::Survey : LoanStatus::Scheduling,
            'surveyor_id' => $surveyor->id,
            'survey_date' => $data['survey_date'],
        ]);

        Notify::toUser(
            $surveyor,
            $walkIn ? 'File ready for analysis' : 'Survey assignment',
            'Scheduling',
            "File {$loanApplication->application_code} ({$loanApplication->full_name}) ".($walkIn ? 'has no field survey and is ready for analysis.' : 'is to be surveyed on '.$loanApplication->survey_date->format('d M Y').'.'),
            $walkIn ? '/analysis' : '/surveys',
        );

        return back()->with('success', $walkIn ? 'Schedule saved. This product has no field survey, so the file is ready for analysis.' : 'Survey schedule saved.');
    }

    /**
     * The assigned surveyor cancels the schedule and asks for a new one; the file goes back to
     * "submitted". Passing the schedule limit only raises a warning.
     */
    public function cancel(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        abort_unless($loanApplication->surveyor_id === $request->user()->id, 403);

        if ($loanApplication->status !== LoanStatus::Scheduling) {
            return back()->with('error', 'This file has no active survey schedule.');
        }

        $data = $request->validate(['reason' => ['required', 'string', 'max:255'], 'return' => ['nullable', 'in:surveys']], [], ['reason' => 'cancellation reason']);
        $done = $this->scheduleCount($loanApplication);

        $this->record($loanApplication, LoanSchedule::ACTION_CANCEL, $data['reason'], $request->user()->name, $done);
        $this->discardSurveyDraft($loanApplication);
        $loanApplication->update(['status' => LoanStatus::Submitted, 'surveyor_id' => null, 'survey_date' => null]);

        $exceeded = $done >= (int) config('credit.max_schedules');

        Notify::toPermission('scheduling.manage', $exceeded ? 'Rescheduling limit exceeded' : 'Reschedule requested', 'Scheduling',
            "File {$loanApplication->application_code}: {$data['reason']}".($exceeded ? " (already scheduled {$done} times)" : ''), '/scheduling', $exceeded ? 'warning' : 'info');

        $message = $exceeded ? "Schedule cancelled. The file has been scheduled {$done} times — please review it." : 'Schedule cancelled; the file awaits a new schedule.';

        // From the survey page the file is no longer the surveyor's, so going back to it would be refused.
        return ($data['return'] ?? null) === 'surveys' ? to_route('surveys.index')->with('success', $message) : back()->with('success', $message);
    }

    /**
     * What the cancelled visit produced (the position marked for the survey location and the photos not yet saved with a survey)
     * belongs to that visit only, so it is discarded: the next surveyor starts clean and cannot meet the requirements with
     * another visit's evidence. Collateral positions stay: they describe the collateral itself.
     */
    private function discardSurveyDraft(LoanApplication $loan): void
    {
        foreach ($loan->photos()->whereNull('loan_survey_id')->get() as $photo) {
            LoanSurveyPhoto::disk()->delete($photo->path);
            $photo->delete();
        }

        $loan->update(['survey_latitude' => null, 'survey_longitude' => null, 'survey_source' => null, 'survey_located_at' => null, 'survey_located_by' => null, 'survey_address' => null]);
    }

    /** Void the application (used when scheduling has dragged on). */
    public function void(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        if (! in_array($loanApplication->status, self::openStatuses(), true)) {
            return back()->with('error', 'This file is not at the scheduling stage.');
        }

        $data = $request->validate(['reason' => ['required', 'string', 'max:255']], [], ['reason' => 'cancellation reason']);

        $this->record($loanApplication, LoanSchedule::ACTION_VOID, $data['reason'], $request->user()->name, $this->scheduleCount($loanApplication));
        $loanApplication->update(['status' => LoanStatus::Cancelled, 'surveyor_id' => null, 'survey_date' => null]);

        Notify::toPermission('loan-applications.manage', 'Application cancelled', 'Scheduling', "File {$loanApplication->application_code}: {$data['reason']}", '/loan-applications', 'warning');

        return back()->with('success', 'Application cancelled.');
    }

    private function record(LoanApplication $loan, string $action, string $reason, string $by, int $sequence): void
    {
        LoanSchedule::create([
            'loan_application_id' => $loan->id, 'sequence' => $sequence, 'action' => $action, 'survey_date' => $loan->survey_date,
            'surveyor_id' => $loan->surveyor_id, 'surveyor_name' => $loan->surveyor?->name, 'reason' => $reason, 'created_by' => $by,
        ]);
    }

    /** Schedules created so far (first schedule plus reschedules). */
    private function scheduleCount(LoanApplication $loan): int
    {
        return $loan->schedules()->whereIn('action', [LoanSchedule::ACTION_SCHEDULE, LoanSchedule::ACTION_RESCHEDULE])->count();
    }

    private function isWalkIn(LoanApplication $loan): bool
    {
        return $loan->product_id !== null && $loan->product->alias === config('credit.walk_in_product');
    }

    /** Role that must survey next; it climbs every time a survey is judged insufficient. */
    private function ladderRole(LoanApplication $loan): string
    {
        $ladder = config('credit.surveyor_ladder');

        return $ladder[min($loan->surveys()->count(), count($ladder) - 1)];
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function surveyors(LoanApplication $loan): array
    {
        $roles = $this->isWalkIn($loan) ? config('credit.walk_in_roles') : [$this->ladderRole($loan)];

        // A committee member who applies for a credit does not survey their own file.
        return User::query()->role($roles)->when($loan->committee_conflict_user_id, fn ($q, int $id) => $q->whereKeyNot($id))->orderBy('name')->get(['id', 'name'])
            ->map(fn (User $u): array => ['value' => $u->id, 'label' => $u->name])->values()->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function row(LoanApplication $loan): array
    {
        $done = $loan->schedules->whereIn('action', [LoanSchedule::ACTION_SCHEDULE, LoanSchedule::ACTION_RESCHEDULE])->count();
        $walkIn = $this->isWalkIn($loan);
        // A file back at "submitted" after a cancellation was sent back by its surveyor, not newly submitted.
        $sentBack = $loan->status === LoanStatus::Submitted ? $loan->schedules->where('action', LoanSchedule::ACTION_CANCEL)->last() : null;

        return [
            'id' => $loan->id,
            'application_code' => $loan->application_code,
            'application_date' => $loan->application_date->toDateString(),
            'full_name' => $loan->full_name,
            'nik' => $loan->nik,
            'status' => $loan->status->value,
            'status_label' => $loan->status->label(),
            'status_tone' => $loan->status->tone(),
            'needs_reschedule' => $sentBack !== null,
            'sent_back_reason' => $sentBack?->reason,
            'product_label' => $loan->product ? "{$loan->product->alias} : {$loan->product->name}" : null,
            'office_label' => $loan->office?->alias,
            'supervisor_name' => $loan->supervisor?->name,
            'surveyor_name' => $loan->surveyor?->name,
            'survey_date' => $loan->survey_date?->toDateString(),
            'requested_amount' => $loan->requested_amount,
            'requested_tenor' => $loan->requested_tenor,
            'schedule_count' => $done,
            'over_limit' => $done >= (int) config('credit.max_schedules'),
            'walk_in' => $walkIn,
            'resurvey' => $loan->status === LoanStatus::Survey,
            'surveyor_role' => $walkIn ? 'Office staff (walk-in)' : $this->ladderRole($loan),
            'surveyor_options' => $this->surveyors($loan),
            'can_cancel' => $loan->status === LoanStatus::Scheduling && $loan->surveyor_id === request()->user()?->id,
            'history' => $loan->schedules->reverse()->values()->map(fn (LoanSchedule $s): array => [
                'id' => $s->id, 'sequence' => $s->sequence, 'action' => LoanSchedule::LABELS[$s->action] ?? $s->action,
                'survey_date' => $s->survey_date?->toDateString(), 'surveyor_name' => $s->surveyor_name, 'note' => $s->note,
                'reason' => $s->reason, 'created_by' => $s->created_by, 'created_at' => $s->created_at?->format('d M Y H:i'),
            ]),
        ];
    }
}
