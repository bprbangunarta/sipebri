<?php

namespace App\Http\Controllers;

use App\Audit\Audit;
use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use App\Models\LoanApproval;
use App\Models\Method;
use App\Models\ProductParameter;
use App\Models\User;
use App\Support\Approvals\ApprovalFlow;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Committee approval of analysed files. Each file has a route of steps (see ApprovalFlow); the person whose turn it is
 * decides it here: pass it up, approve, reject or cancel.
 */
class ApprovalController extends Controller
{
    private const PER_PAGE_OPTIONS = [10, 25, 50];

    private const STATUSES = ['committee', 'approved', 'rejected', 'cancelled'];

    public function index(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in([...self::STATUSES, 'all'])],
            'scope' => ['nullable', Rule::in(['mine', 'all'])],
            'per_page' => ['nullable', 'integer'],
        ]);
        $status = $filters['status'] ?? 'committee';
        // People who decide see what waits for them first; everyone else sees every file they may open.
        $scope = $filters['scope'] ?? 'mine';

        $loans = $this->visibleTo($user)
            ->with(['product:id,alias,name', 'analysis:id,loan_application_id,submitted_at', 'analysis.memorandum'])
            ->when($status !== 'all', fn (Builder $q) => $q->where('status', $status))
            ->when($scope === 'mine', fn (Builder $q) => $this->waitingFor($q, $user))
            ->when($filters['search'] ?? null, function (Builder $q, string $term) {
                $like = '%'.addcslashes($term, '%_\\').'%';
                $q->where(fn ($w) => $w->where('application_code', 'like', $like)->orWhere('full_name', 'like', $like)->orWhere('nik', 'like', $like));
            })
            ->orderByDesc('id')
            ->paginate(in_array((int) ($filters['per_page'] ?? 0), self::PER_PAGE_OPTIONS, true) ? (int) $filters['per_page'] : 10)
            ->withQueryString();

        return Inertia::render('approvals/index', [
            'loans' => $loans->through(function (LoanApplication $l) use ($user): array {
                $pending = $l->status === LoanStatus::Committee ? ApprovalFlow::pending($l) : null;

                return [
                    ...$l->only(['id', 'application_code', 'full_name', 'nik']),
                    'product_label' => $l->product ? "{$l->product->alias} : {$l->product->name}" : null,
                    'proposed_amount' => $l->analysis->memorandum->proposed_amount ?? $l->requested_amount,
                    'proposed_tenor' => $l->analysis->memorandum->term_months ?? $l->requested_tenor,
                    'status' => $l->status->value,
                    'status_label' => $l->status->label(),
                    'pending_position' => $pending ? trim("{$pending->tier_label} · {$pending->role}", ' ·') : null,
                    'my_turn' => $pending !== null && ApprovalFlow::mayDecide($user, $pending, $l),
                    'submitted_at' => $l->analysis?->submitted_at?->toDateString(),
                ];
            }),
            'filters' => ['search' => $filters['search'] ?? '', 'status' => $status, 'scope' => $scope, 'per_page' => $loans->perPage()],
            'perPageOptions' => self::PER_PAGE_OPTIONS,
            'statuses' => [
                ['value' => 'committee', 'label' => 'Menunggu keputusan'],
                ['value' => 'approved', 'label' => 'Disetujui'],
                ['value' => 'rejected', 'label' => 'Ditolak'],
                ['value' => 'cancelled', 'label' => 'Dibatalkan'],
                ['value' => 'all', 'label' => 'Semua status'],
            ],
        ]);
    }

    public function show(Request $request, LoanApplication $loanApplication): Response
    {
        /** @var User $user */
        $user = $request->user();
        $this->authorizeView($user, $loanApplication);
        Audit::record('approvals.viewed', 'approvals', 'viewed', $loanApplication, label: $loanApplication->application_code);

        $loanApplication->load(['product:id,alias,name', 'office:id,alias', 'supervisor:id,name', 'surveyor:id,name', 'approvals.method:id,name', 'analysis.submitter:id,name']);
        $basis = ApprovalFlow::basis($loanApplication);
        $pending = $loanApplication->status === LoanStatus::Committee ? ApprovalFlow::pending($loanApplication) : null;
        $myTurn = $pending !== null && ApprovalFlow::mayDecide($user, $pending, $loanApplication);
        $allowed = $myTurn ? ApprovalFlow::allowed($pending, $basis['amount']) : [];
        $last = $loanApplication->approvals->whereNotNull('decision')->last();

        return Inertia::render('approvals/show', [
            'record' => [
                ...$loanApplication->only(['id', 'application_code', 'full_name', 'nik', 'requested_amount', 'requested_tenor', 'usage_type']),
                'application_date' => $loanApplication->application_date->toDateString(),
                'status' => $loanApplication->status->value,
                'status_label' => $loanApplication->status->label(),
                'product_label' => $loanApplication->product ? "{$loanApplication->product->alias} : {$loanApplication->product->name}" : null,
                'office_label' => $loanApplication->office?->alias,
                'supervisor_name' => $loanApplication->supervisor?->name,
                'surveyor_name' => $loanApplication->surveyor?->name,
                'submitted_at' => $loanApplication->analysis?->submitted_at?->isoFormat('D MMM YYYY HH:mm'),
                'submitted_by' => $loanApplication->analysis?->submitter?->name,
                'exception' => $loanApplication->committee_exception,
                'decision_note' => $loanApplication->decision_note,
                'approved_amount' => $loanApplication->approved_amount,
                'approved_tenor' => $loanApplication->approved_tenor,
                'approved_rate' => $loanApplication->approved_rate,
            ],
            'basis' => $basis,
            'steps' => $loanApplication->approvals->map(fn (LoanApproval $s): array => [
                'id' => $s->id,
                'sort' => $s->sort,
                'label' => $s->tier_label,
                'role' => $s->role,
                'individual' => $s->is_individual,
                'decision' => $s->decision,
                'decision_label' => $s->decision ? LoanApproval::LABELS[$s->decision] : null,
                'decided_by' => $s->decided_by_name,
                'decided_at' => $s->decided_at?->isoFormat('D MMM YYYY HH:mm'),
                'method_label' => $s->method?->name,
                'amount' => $s->amount,
                'tenor' => $s->tenor,
                'interest_rate' => $s->interest_rate,
                'provision_rate' => $s->provision_rate,
                'admin_rate' => $s->admin_rate,
                'max_amount' => $s->max_amount,
                'rc_ratio' => $s->rc_ratio,
                'note' => $s->note,
                'pending' => $s->isPending(),
            ])->values()->all(),
            'flow' => [
                'pending_label' => $pending ? "{$pending->tier_label} · {$pending->role}" : null,
                'my_turn' => $myTurn,
                'allowed' => $allowed,
                'blocked_reason' => $this->blockedReason($pending, $myTurn, $allowed, $loanApplication),
            ],
            'form' => [
                'method_id' => $last->method_id ?? $basis['method_id'],
                'amount' => $last->amount ?? $basis['amount'],
                'tenor' => $last->tenor ?? $basis['tenor'],
                'interest_rate' => $last->interest_rate ?? $basis['interest_rate'],
                'provision_rate' => $last->provision_rate ?? $basis['provision_rate'],
                'admin_rate' => $last->admin_rate ?? $basis['admin_rate'],
            ],
            'methodOptions' => $this->methodOptions($loanApplication),
            'decisionLabels' => LoanApproval::LABELS,
        ]);
    }

    public function decide(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless($loanApplication->status === LoanStatus::Committee, 404, 'Berkas tidak menunggu keputusan komite.');

        $step = ApprovalFlow::pending($loanApplication);
        abort_if($step === null, 422, 'Jalur komite berkas ini belum punya pemutus.');
        abort_unless(ApprovalFlow::mayDecide($user, $step, $loanApplication), 403, 'Anda bukan pemutus pada jenjang ini.');

        $percent = ['required', 'numeric', 'min:0', 'max:100'];
        $data = $request->validate([
            'decision' => ['required', Rule::in(ApprovalFlow::allowed($step, (int) $request->input('amount')))],
            'method_id' => ['required', 'integer', 'exists:methods,id'],
            'amount' => ['required', 'integer', 'min:1', 'max:999999999999'],
            'tenor' => ['required', 'integer', 'min:1', 'max:300'],
            'interest_rate' => $percent,
            'provision_rate' => $percent,
            'admin_rate' => $percent,
            'note' => ['nullable', 'string', 'max:255'],
        ], [
            'decision.in' => 'Keputusan ini tidak diizinkan pada jenjang Anda untuk plafon tersebut.',
        ], [
            'decision' => 'keputusan', 'method_id' => 'metode bunga', 'amount' => 'plafon', 'tenor' => 'jangka waktu',
            'interest_rate' => 'suku bunga', 'provision_rate' => 'biaya provisi', 'admin_rate' => 'biaya admin', 'note' => 'catatan',
        ]);

        ApprovalFlow::decide($loanApplication, $step, $user, $data);

        $label = LoanApproval::LABELS[$data['decision']];

        return to_route('approvals.index')->with('success', "Berkas {$loanApplication->application_code}: {$label}.");
    }

    /**
     * The files this person may open: the ones they decide a step of, work on, or supervise. Super Admin sees all.
     *
     * @return Builder<LoanApplication>
     */
    private function visibleTo(User $user): Builder
    {
        $query = LoanApplication::query()->whereIn('status', [LoanStatus::Committee, LoanStatus::Approved, LoanStatus::Rejected, LoanStatus::Cancelled]);

        if ($user->hasRole('Super Admin')) {
            return $query;
        }

        $roles = $user->getRoleNames()->all();

        return $query->where(fn (Builder $q) => $q
            ->where('surveyor_id', $user->id)
            ->orWhere('supervisor_id', $user->id)
            ->orWhereHas('approvals', fn (Builder $a) => $a->whereIn('role', $roles)));
    }

    /**
     * Files whose pending step is the one this person decides.
     *
     * @param  Builder<LoanApplication>  $query
     * @return Builder<LoanApplication>
     */
    private function waitingFor(Builder $query, User $user): Builder
    {
        $roles = $user->getRoleNames()->all();

        return $query->where('status', LoanStatus::Committee)
            ->where(fn (Builder $q) => $q->whereNull('committee_conflict_user_id')->orWhere('committee_conflict_user_id', '!=', $user->id))
            ->whereHas('approvals', fn (Builder $a) => $a
                ->whereNull('decision')
                ->whereRaw('sort = (select min(sort) from loan_approvals as next where next.loan_application_id = loan_approvals.loan_application_id and next.decision is null)')
                ->where(fn (Builder $w) => $w
                    ->where(fn (Builder $c) => $c->where('is_individual', false)->when(! $user->hasRole('Super Admin'), fn (Builder $r) => $r->whereIn('role', $roles)))
                    ->orWhere(fn (Builder $i) => $i->where('is_individual', true)->whereHas('loanApplication', fn (Builder $l) => $l->where('surveyor_id', $user->id)))));
    }

    private function authorizeView(User $user, LoanApplication $loan): void
    {
        abort_unless($this->visibleTo($user)->whereKey($loan->id)->exists(), 403, 'Berkas ini bukan untuk Anda.');
    }

    /**
     * @param  list<string>  $allowed
     */
    private function blockedReason(?LoanApproval $pending, bool $myTurn, array $allowed, LoanApplication $loan): ?string
    {
        if ($loan->status !== LoanStatus::Committee) {
            return null;
        }

        if ($pending === null) {
            return 'Jalur komite berkas ini belum punya pemutus.';
        }

        if (! $myTurn) {
            return "Berkas menunggu keputusan {$pending->tier_label} ({$pending->role}).";
        }

        return $allowed === [] ? 'Jenjang ini tidak punya kewenangan keputusan untuk plafon tersebut.' : null;
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function methodOptions(LoanApplication $loan): array
    {
        $allowed = ProductParameter::query()->where('product_id', $loan->product_id)->value('allowed_method_ids');

        return Method::query()
            ->when(is_array($allowed) && $allowed !== [], fn ($q) => $q->whereIn('id', $allowed))
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Method $m): array => ['value' => $m->id, 'label' => $m->name])
            ->all();
    }
}
