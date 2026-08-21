<?php

namespace App\Http\Controllers;

use App\Models\AnalysisBusiness;
use App\Models\AnalysisSheet;
use App\Models\AnalysisSheetItem;
use App\Models\LoanApplication;
use App\Models\LoanSurvey;
use App\Support\TableQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Analisa Kredit — tahap 4 dari alur kredit.
 * Menampilkan berkas berstatus SURVEY milik petugas yang ditugaskan.
 * Lembar analisa berisi 8 bagian; Analisa Usaha dikelola AnalysisBusinessController.
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

    /** Lembar analisa berkas. */
    public function show(Request $request, LoanApplication $loanApplication): Response
    {
        $this->authorizeAnalyst($request, $loanApplication);

        $businesses = AnalysisBusiness::where('loan_application_id', $loanApplication->id)
            ->orderBy('id')
            ->get(['id', 'type', 'code', 'name', 'revenue', 'expense', 'net_profit', 'monthly_income'])
            ->map(fn (AnalysisBusiness $b) => [
                'id' => $b->id,
                'type' => $b->type,
                'code' => $b->code,
                'name' => $b->name,
                'revenue' => (int) $b->revenue,
                'expense' => (int) $b->expense,
                'net_profit' => (int) $b->net_profit,
                'monthly_income' => (int) $b->monthly_income,
            ])
            ->all();

        return Inertia::render('AnalysisDetail', [
            'record' => $this->row($loanApplication),
            'businesses' => $businesses,
            'sheet' => $this->sheetPayload($this->sheet($loanApplication)),
            'options' => [
                'assets' => AnalysisSheet::ASSETS,
            ],
        ]);
    }

    /** Analisa Keuangan: biaya rumah tangga + kewajiban lain. */
    public function updateFinance(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        $this->authorizeAnalyst($request, $loanApplication);

        $money = ['nullable', 'integer', 'min:0', 'max:999999999999'];

        $data = $request->validate(array_merge(
            array_fill_keys(AnalysisSheet::HOUSEHOLD, $money),
            [
                'items' => ['nullable', 'array', 'max:20'],
                'items.*.name' => ['required', 'string', 'max:150'],
                'items.*.amount' => $money,
            ],
        ), [], ['items.*.name' => 'nama kewajiban', 'items.*.amount' => 'nominal kewajiban']);

        $sheet = $this->sheet($loanApplication);
        $sheet->fill(collect($data)->except('items')->map(fn ($v) => $v ?? 0)->all())->save();

        $this->syncItems($sheet, 'OBLIGATION', $data['items'] ?? []);

        return back()->with('success', 'Analisa keuangan disimpan.');
    }

    /** Analisa Kepemilikan: harta pemohon. */
    public function updateOwnership(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        $this->authorizeAnalyst($request, $loanApplication);

        $rules = collect(AnalysisSheet::ASSETS)
            ->map(fn (array $options) => ['nullable', Rule::in($options)])
            ->all();

        $data = $request->validate(array_merge($rules, [
            'items' => ['nullable', 'array', 'max:20'],
            'items.*.name' => ['required', 'string', 'max:150'],
        ]), [], ['items.*.name' => 'nama harta']);

        $sheet = $this->sheet($loanApplication);
        $sheet->fill(collect($data)->except('items')->all())->save();

        $this->syncItems($sheet, 'ASSET', $data['items'] ?? []);

        return back()->with('success', 'Analisa kepemilikan disimpan.');
    }

    private function sheet(LoanApplication $r): AnalysisSheet
    {
        return AnalysisSheet::firstOrCreate(['loan_application_id' => $r->id]);
    }

    private function syncItems(AnalysisSheet $sheet, string $group, array $items): void
    {
        AnalysisSheetItem::where('analysis_sheet_id', $sheet->id)->where('group', $group)->delete();

        foreach (array_values($items) as $sort => $item) {
            AnalysisSheetItem::create([
                'analysis_sheet_id' => $sheet->id,
                'group' => $group,
                'name' => Str::upper($item['name']),
                'amount' => $item['amount'] ?? 0,
                'sort' => $sort,
            ]);
        }
    }

    private function sheetPayload(AnalysisSheet $sheet): array
    {
        $sheet->load('items');

        return [
            ...$sheet->only([...AnalysisSheet::HOUSEHOLD, ...array_keys(AnalysisSheet::ASSETS)]),
            'obligations' => $sheet->items->where('group', 'OBLIGATION')
                ->map(fn (AnalysisSheetItem $i) => ['name' => $i->name, 'amount' => (int) $i->amount])
                ->values()->all(),
            'assets' => $sheet->items->where('group', 'ASSET')
                ->map(fn (AnalysisSheetItem $i) => ['name' => $i->name])
                ->values()->all(),
            'metrics' => $sheet->metrics(),
            'updated_at' => $sheet->updated_at?->translatedFormat('d M Y H:i'),
            'updated_by' => $sheet->updated_by ?? $sheet->created_by,
        ];
    }

    private function authorizeAnalyst(Request $request, LoanApplication $r): void
    {
        if ($r->surveyor_id !== $request->user()->id) {
            throw new NotFoundHttpException('Berkas ini bukan penugasan Anda.');
        }
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
