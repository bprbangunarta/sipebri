<?php

namespace App\Http\Controllers;

use App\Audit\Audit;
use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use App\Models\LoanSurvey;
use App\Models\User;
use App\Support\CreditAnalysis\AnalysisAccess;
use App\Support\CreditAnalysis\AnalysisPayload;
use App\Support\CreditAnalysis\AnalysisTemplates;
use App\Support\Notify;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Credit analysis. The list holds the files assigned to the signed-in surveyor that are ready to be analysed (or being
 * analysed); a file opens into its worksheet, whose sections are saved by their own controllers, and goes to the
 * committee from here.
 */
class CreditAnalysisController extends Controller
{
    private const PER_PAGE_OPTIONS = [10, 25, 50];

    private const SORTABLE = ['application_code', 'application_date', 'full_name', 'requested_amount', 'survey_date'];

    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'string'], 'direction' => ['nullable', 'in:asc,desc'], 'per_page' => ['nullable', 'integer'],
        ]);
        $sort = in_array($filters['sort'] ?? null, self::SORTABLE, true) ? $filters['sort'] : 'application_code';
        $direction = $filters['direction'] ?? 'desc';

        $loans = LoanApplication::query()
            ->with(['product:id,alias,name', 'office:id,alias', 'supervisor:id,name'])
            ->withCount(['surveys', 'collaterals'])
            ->whereIn('status', [LoanStatus::Survey, LoanStatus::Analysis])
            ->where('surveyor_id', $request->user()->id)
            ->when($filters['search'] ?? null, function ($q, string $term) {
                $like = '%'.addcslashes($term, '%_\\').'%';
                $q->where(fn ($w) => $w->where('application_code', 'like', $like)->orWhere('full_name', 'like', $like)->orWhere('nik', 'like', $like));
            })
            ->orderBy($sort, $direction)->orderBy('id')
            ->paginate(in_array((int) ($filters['per_page'] ?? 0), self::PER_PAGE_OPTIONS, true) ? (int) $filters['per_page'] : 10)
            ->withQueryString();

        return Inertia::render('credit-analysis/index', [
            'loans' => $loans->through(fn (LoanApplication $l): array => [
                ...$l->only(['id', 'application_code', 'full_name', 'nik', 'requested_amount', 'requested_tenor']),
                'application_date' => $l->application_date->toDateString(),
                'survey_date' => $l->survey_date?->toDateString(),
                'product_label' => $l->product ? "{$l->product->alias} : {$l->product->name}" : null,
                'office_label' => $l->office?->alias,
                'supervisor_name' => $l->supervisor?->name,
                'surveyed' => $l->surveys_count > 0,
                'collaterals_count' => $l->collaterals_count,
                'in_analysis' => $l->status === LoanStatus::Analysis,
            ]),
            'filters' => ['search' => $filters['search'] ?? '', 'sort' => $sort, 'direction' => $direction, 'per_page' => $loans->perPage()],
            'perPageOptions' => self::PER_PAGE_OPTIONS,
        ]);
    }

    public function show(Request $request, LoanApplication $loanApplication): Response
    {
        /** @var User $user */
        $user = $request->user();
        AnalysisAccess::view($user, $loanApplication);
        Audit::record('credit_analyses.viewed', 'credit_analyses', 'viewed', $loanApplication, label: $loanApplication->application_code);

        $loanApplication->load(['product', 'office', 'supervisor', 'installment', 'collaterals', 'analysis.submitter']);
        $analysis = $loanApplication->analysis;

        return Inertia::render('credit-analysis/show', [
            'record' => $this->record($loanApplication),
            'sections' => AnalysisTemplates::sections($analysis->template ?? AnalysisTemplates::for($loanApplication)),
            'canEdit' => $loanApplication->surveyor_id === $user->id && in_array($loanApplication->status, [LoanStatus::Survey, LoanStatus::Analysis], true),
            ...AnalysisPayload::build($loanApplication, $analysis),
        ]);
    }

    /** Send the worksheet to the committee, once the parts every file needs are filled in. */
    public function submit(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $analysis = AnalysisAccess::edit($user, $loanApplication);

        if ($gaps = $analysis->gaps()) {
            return back()->with('error', 'Lengkapi dulu: '.implode(', ', $gaps).'.');
        }

        $analysis->update(['submitted_at' => now(), 'submitted_by' => $user->id]);
        $loanApplication->update(['status' => LoanStatus::Committee]);

        $message = "Lembar analisa berkas {$loanApplication->application_code} ({$loanApplication->full_name}) sudah diajukan ke komite kredit.";

        if ($loanApplication->supervisor) {
            Notify::toUser($loanApplication->supervisor, 'Berkas siap diputus komite', 'Analisa Kredit', $message, '/credit-analysis');
        }

        Notify::toPermission('approvals.view', 'Berkas masuk komite kredit', 'Analisa Kredit', "Berkas {$loanApplication->application_code} - {$loanApplication->full_name} menunggu keputusan komite.", '/approvals');

        return to_route('credit-analysis.index')->with('success', "Berkas {$loanApplication->application_code} diajukan ke komite kredit.");
    }

    /**
     * @return array<string, mixed>
     */
    private function record(LoanApplication $loan): array
    {
        $survey = LoanSurvey::query()->where('loan_application_id', $loan->id)->latest('sequence')->first();

        return [
            ...$loan->only(['id', 'application_code', 'full_name', 'nik', 'requested_amount', 'requested_tenor', 'interest_rate', 'usage_type']),
            'status' => $loan->status->value,
            'status_label' => $loan->status->label(),
            'product_label' => $loan->product ? "{$loan->product->alias} : {$loan->product->name}" : null,
            'office_label' => $loan->office?->alias,
            'supervisor_name' => $loan->supervisor?->name,
            'installment_label' => $loan->installment?->name,
            'installment_period' => $loan->installment->period_months ?? 0,
            'survey_note' => $survey?->note,
            'survey_by' => $survey?->surveyor_name,
            'survey_at' => $survey?->created_at?->isoFormat('D MMM YYYY HH:mm'),
        ];
    }
}
