<?php

namespace App\Http\Controllers;

use App\Models\LoanApplication;
use App\Models\LoanSurvey;
use App\Support\TableQuery;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Analisa Kredit — tahap 4 dari alur kredit.
 * Menampilkan berkas berstatus SURVEY milik petugas yang ditugaskan.
 * Form analisa belum dibangun (halaman detail masih placeholder).
 */
class AnalysisController extends Controller
{
    public function index(Request $request): Response
    {
        $search = TableQuery::search($request);
        $sort = TableQuery::sort($request, ['application_code', 'application_date', 'full_name', 'requested_amount'], 'application_code');
        $dir = TableQuery::direction($request);

        $records = LoanApplication::query()
            ->with(['product:id,alias,name', 'office:id,alias,name', 'supervisor:id,name'])
            ->where('status', 'SURVEY')
            ->where('surveyor_id', $request->user()->id)
            ->when($search !== '', fn ($q) => $q->where(function ($w) use ($search) {
                foreach (['application_code', 'full_name', 'nik', 'requested_amount', 'requested_tenor'] as $col) {
                    $w->orWhere($col, 'like', "%{$search}%");
                }

                $w->orWhereHas('product', fn ($p) => $p
                    ->where('alias', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%"));
            }))
            ->orderBy($sort, $dir)
            ->paginate(TableQuery::perPage($request))
            ->withQueryString();

        return Inertia::render('AnalysisSimulation', [
            'records' => [
                'data' => collect($records->items())->map(fn (LoanApplication $r) => $this->row($r))->all(),
                'meta' => TableQuery::meta($records),
            ],
            'filters' => ['search' => $search, 'sort' => $sort, 'dir' => $dir],
        ]);
    }

    /** Lembar analisa — masih placeholder sampai formnya dibahas. */
    public function show(Request $request, LoanApplication $loanApplication): Response
    {
        if ($loanApplication->surveyor_id !== $request->user()->id) {
            throw new NotFoundHttpException('Berkas ini bukan penugasan Anda.');
        }

        return Inertia::render('AnalysisDetail', [
            'record' => $this->row($loanApplication),
        ]);
    }

    private function row(LoanApplication $r): array
    {
        $survey = LoanSurvey::where('loan_application_id', $r->id)->latest('id')->first();

        return [
            'id' => $r->id,
            'application_code' => $r->application_code,
            'application_date' => $r->application_date?->translatedFormat('d M Y'),
            'full_name' => $r->full_name,
            'nik' => $r->nik,
            'status' => $r->status,
            'product_label' => $r->product ? "{$r->product->alias} : {$r->product->name}" : null,
            'office_label' => $r->office ? "{$r->office->alias} : {$r->office->name}" : null,
            'supervisor_name' => $r->supervisor?->name,
            'usage_type' => $r->usage_type,
            'requested_amount' => $r->requested_amount,
            'requested_tenor' => $r->requested_tenor,
            'interest_rate' => $r->interest_rate,
            'survey_count' => LoanSurvey::where('loan_application_id', $r->id)->count(),
            'survey_note' => $survey?->note,
            'survey_by' => $survey?->surveyor_name,
            'survey_at' => $survey?->created_at?->translatedFormat('d M Y H:i'),
        ];
    }
}
