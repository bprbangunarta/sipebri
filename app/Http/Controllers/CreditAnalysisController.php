<?php

namespace App\Http\Controllers;

use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Credit analysis, stage 4. For now this only lists the files that are ready to be analysed:
 * surveyed files (or walk-in files, which skip the visit) assigned to the current user.
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
            ->where('status', LoanStatus::Survey)
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
            ]),
            'filters' => ['search' => $filters['search'] ?? '', 'sort' => $sort, 'direction' => $direction, 'per_page' => $loans->perPage()],
            'perPageOptions' => self::PER_PAGE_OPTIONS,
        ]);
    }
}
