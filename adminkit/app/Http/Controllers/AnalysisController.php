<?php

namespace App\Http\Controllers;

use App\Models\AnalysisAdministration;
use App\Models\AnalysisBusiness;
use App\Models\AnalysisCollateral;
use App\Models\AnalysisFiveC;
use App\Models\AnalysisMemorandum;
use App\Models\AnalysisQualitative;
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
            'fiveC' => $this->fiveCPayload($loanApplication),
            'qualitative' => $this->qualitativePayload($loanApplication),
            'collaterals' => $this->collateralPayload($loanApplication),
            'memorandum' => $this->memorandumPayload($loanApplication),
            'administration' => $this->administrationPayload($loanApplication),
            'options' => [
                'assets' => AnalysisSheet::ASSETS,
                'qualitativeChoices' => AnalysisQualitative::CHOICES,
                'collateralKinds' => AnalysisCollateral::KINDS,
                'bindings' => AnalysisMemorandum::BINDINGS,
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

    /** Analisa 5C — hanya skor; kolom evaluasi dihitung sistem. */
    public function updateFiveC(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        $this->authorizeAnalyst($request, $loanApplication);

        $rules = [];

        foreach (AnalysisFiveC::ASPECTS as $aspects) {
            foreach ($aspects as $column => $scale) {
                $rules[$column] = ['nullable', 'integer', 'min:0', "max:{$scale}"];
            }
        }

        $data = $request->validate($rules);

        $record = AnalysisFiveC::firstOrCreate(['loan_application_id' => $loanApplication->id]);
        $record->fill($data)->save();

        return back()->with('success', 'Analisa 5C disimpan.');
    }

    /** Analisa Kualitatif. */
    public function updateQualitative(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        $this->authorizeAnalyst($request, $loanApplication);

        $rules = [];

        foreach (AnalysisQualitative::SCORES as $column => $scale) {
            $rules[$column] = ['nullable', 'integer', 'min:1', "max:{$scale}"];
        }

        foreach (AnalysisQualitative::CHOICES as $column => $choices) {
            $rules[$column] = ['nullable', Rule::in($choices)];
        }

        foreach (AnalysisQualitative::TEXTS as $column) {
            $rules[$column] = ['nullable', 'string', 'max:255'];
        }

        foreach (AnalysisQualitative::NOTES as $column) {
            $rules[$column] = ['nullable', 'string', 'max:2000'];
        }

        $data = collect($request->validate($rules))
            ->map(fn ($value, $key) => is_string($value) ? Str::upper($value) : $value)
            ->all();

        $record = AnalysisQualitative::firstOrCreate(['loan_application_id' => $loanApplication->id]);
        $record->fill($data)->save();

        return back()->with('success', 'Analisa kualitatif disimpan.');
    }

    /** Bagian 4 — berita acara pemeriksaan tiap agunan berkas. */
    public function updateCollaterals(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        $this->authorizeAnalyst($request, $loanApplication);

        $ids = $loanApplication->collaterals()->pluck('collateral_simulations.id')->all();

        $data = $request->validate([
            'rows' => ['required', 'array'],
            'rows.*.collateral_simulation_id' => ['required', Rule::in($ids)],
            'rows.*.kind' => ['required', Rule::in(AnalysisCollateral::KINDS)],
            'rows.*.merek' => ['nullable', 'string', 'max:100'],
            'rows.*.tipe_kendaraan' => ['nullable', 'string', 'max:100'],
            'rows.*.tahun' => ['nullable', 'digits:4'],
            'rows.*.no_rangka' => ['nullable', 'string', 'max:50'],
            'rows.*.no_mesin' => ['nullable', 'string', 'max:50'],
            'rows.*.no_polisi' => ['nullable', 'string', 'max:20'],
            'rows.*.warna' => ['nullable', 'string', 'max:50'],
            'rows.*.luas' => ['nullable', 'integer', 'min:0', 'max:99999999'],
            'rows.*.lokasi' => ['nullable', 'string', 'max:255'],
            'rows.*.market_value' => ['nullable', 'integer', 'min:0', 'max:999999999999'],
            'rows.*.appraisal_value' => ['nullable', 'integer', 'min:0', 'max:999999999999'],
            'rows.*.catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        foreach ($data['rows'] as $row) {
            $values = collect($row)->except('collateral_simulation_id')
                ->map(fn ($value, $key) => is_string($value) ? Str::upper($value) : ($value ?? 0))
                ->all();

            AnalysisCollateral::updateOrCreate(
                [
                    'loan_application_id' => $loanApplication->id,
                    'collateral_simulation_id' => $row['collateral_simulation_id'],
                ],
                $values,
            );
        }

        return back()->with('success', 'Analisa agunan disimpan.');
    }

    /** Bagian 7 — memorandum: kebutuhan dana & usulan fasilitas. */
    public function updateMemorandum(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        $this->authorizeAnalyst($request, $loanApplication);

        $money = ['nullable', 'integer', 'min:0', 'max:999999999999'];
        $rules = array_merge(
            array_fill_keys(AnalysisMemorandum::NEEDS, $money),
            array_fill_keys(
                array_map(fn ($c) => "ket_{$c}", AnalysisMemorandum::NEEDS),
                ['nullable', 'string', 'max:255'],
            ),
            array_fill_keys(AnalysisMemorandum::RATES, ['nullable', 'numeric', 'min:0', 'max:100']),
            [
                'usulan_plafond' => $money,
                'jangka_waktu' => ['nullable', 'integer', 'min:0', 'max:600'],
                'sebelum_realisasi' => ['nullable', 'string', 'max:255'],
                'syarat_tambahan' => ['nullable', 'string', 'max:255'],
                'pengikatan' => ['nullable', Rule::in(AnalysisMemorandum::BINDINGS)],
            ],
        );

        $data = collect($request->validate($rules))
            ->map(fn ($value, $key) => is_string($value) ? Str::upper($value) : ($value ?? 0))
            ->all();

        $record = AnalysisMemorandum::firstOrCreate(['loan_application_id' => $loanApplication->id]);
        $record->fill($data)->save();

        return back()->with('success', 'Memorandum disimpan.');
    }

    /** Bagian 8 — administrasi: rincian biaya. */
    public function updateAdministration(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        $this->authorizeAnalyst($request, $loanApplication);

        $data = collect($request->validate(array_fill_keys(
            AnalysisAdministration::FEES,
            ['nullable', 'integer', 'min:0', 'max:999999999999'],
        )))->map(fn ($value) => $value ?? 0)->all();

        $record = AnalysisAdministration::firstOrCreate(['loan_application_id' => $loanApplication->id]);
        $record->fill($data)->save();

        return back()->with('success', 'Administrasi disimpan.');
    }

    private function collateralPayload(LoanApplication $r): array
    {
        $checks = AnalysisCollateral::where('loan_application_id', $r->id)
            ->get()->keyBy('collateral_simulation_id');

        return $r->collaterals->map(function ($collateral) use ($checks) {
            $check = $checks->get($collateral->id);

            return [
                'collateral_simulation_id' => $collateral->id,
                'label' => $collateral->description,
                'document_number' => $collateral->document_number,
                'owner_name' => $collateral->owner_name,
                'cbs_appraisal' => (int) $collateral->appraisal_value,
                'kind' => $check?->kind ?? 'LAINNYA',
                'merek' => $check?->merek ?? '',
                'tipe_kendaraan' => $check?->tipe_kendaraan ?? '',
                'tahun' => $check?->tahun ?? '',
                'no_rangka' => $check?->no_rangka ?? '',
                'no_mesin' => $check?->no_mesin ?? '',
                'no_polisi' => $check?->no_polisi ?? '',
                'warna' => $check?->warna ?? '',
                'luas' => (int) ($check?->luas ?? 0),
                'lokasi' => $check?->lokasi ?? '',
                'market_value' => (int) ($check?->market_value ?? 0),
                'appraisal_value' => (int) ($check?->appraisal_value ?? $collateral->appraisal_value),
                'catatan' => $check?->catatan ?? '',
            ];
        })->values()->all();
    }

    private function memorandumPayload(LoanApplication $r): array
    {
        $record = AnalysisMemorandum::firstOrNew(['loan_application_id' => $r->id]);
        $columns = [
            ...AnalysisMemorandum::NEEDS,
            ...array_map(fn ($c) => "ket_{$c}", AnalysisMemorandum::NEEDS),
            ...AnalysisMemorandum::RATES,
            'usulan_plafond', 'jangka_waktu', 'sebelum_realisasi', 'syarat_tambahan', 'pengikatan',
        ];

        return [
            ...collect($columns)->mapWithKeys(fn ($c) => [$c => $record->{$c} ?? ($record->exists ? null : 0)])->all(),
            'requested_amount' => (int) $r->requested_amount,
            'requested_tenor' => (int) $r->requested_tenor,
            'taksasi' => (int) $r->collaterals()->sum('appraisal_value'),
            'monthly_balance' => $this->sheet($r)->metrics()['monthly_balance'],
            'updated_at' => $record->updated_at?->translatedFormat('d M Y H:i'),
        ];
    }

    private function administrationPayload(LoanApplication $r): array
    {
        $record = AnalysisAdministration::firstOrNew(['loan_application_id' => $r->id]);

        return [
            ...collect(AnalysisAdministration::FEES)->mapWithKeys(fn ($c) => [$c => (int) $record->{$c}])->all(),
            'total' => $record->total(),
            'updated_at' => $record->updated_at?->translatedFormat('d M Y H:i'),
        ];
    }

    private function fiveCPayload(LoanApplication $r): array
    {
        $record = AnalysisFiveC::firstOrNew(['loan_application_id' => $r->id]);
        $columns = collect(AnalysisFiveC::ASPECTS)->flatMap(fn ($a) => array_keys($a))->all();

        return [
            ...collect($columns)->mapWithKeys(fn ($c) => [$c => $record->{$c}])->all(),
            'metrics' => $record->metrics(),
            'taksasi' => (int) $r->collaterals()->sum('appraisal_value'),
            'updated_at' => $record->updated_at?->translatedFormat('d M Y H:i'),
        ];
    }

    private function qualitativePayload(LoanApplication $r): array
    {
        $record = AnalysisQualitative::firstOrNew(['loan_application_id' => $r->id]);

        return [
            ...collect(AnalysisQualitative::columns())->mapWithKeys(fn ($c) => [$c => $record->{$c}])->all(),
            'updated_at' => $record->updated_at?->translatedFormat('d M Y H:i'),
        ];
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
