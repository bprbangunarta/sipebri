<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\BindingType;
use App\Models\CollateralCondition;
use App\Models\CollateralMethod;
use App\Models\CollateralSimulation;
use App\Models\CollateralType;
use App\Models\Region;
use App\Support\TableQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Contoh/simulasi data agunan mengikuti form CBS.
 * TIDAK ADA PERHITUNGAN di sini — data hanya direkam lalu dikirim ke CBS via API.
 */
class CollateralSimulationController extends Controller
{
    private const LABEL = 'Agunan Kredit';

    public function index(Request $request): Response
    {
        $search = TableQuery::search($request);
        $sort = TableQuery::sort($request, ['collateral_id', 'collateral_type_code', 'owner_name'], 'collateral_id');
        $dir = TableQuery::direction($request);

        $records = CollateralSimulation::query()
            ->when($search !== '', fn ($q) => $q->where(function ($w) use ($search) {
                foreach (['collateral_id', 'owner_name', 'description', 'document_number'] as $col) {
                    $w->orWhere($col, 'like', "%{$search}%");
                }
            }))
            ->orderBy($sort, $dir)
            ->paginate(TableQuery::perPage($request))
            ->withQueryString();

        return Inertia::render('CollateralSimulation', [
            'records' => [
                'data' => collect($records->items())->map(fn (CollateralSimulation $r) => $this->row($r))->all(),
                'meta' => TableQuery::meta($records),
            ],
            'filters' => ['search' => $search, 'sort' => $sort, 'dir' => $dir],
        ]);
    }

    public function show(CollateralSimulation $collateralSimulation): Response
    {
        // Halaman ini untuk developer: menampilkan kolom mentah tabel + payload CBS.
        $values = $collateralSimulation->getAttributes();

        $columns = collect(Schema::getColumns($collateralSimulation->getTable()))
            ->map(fn (array $c) => [
                'name' => $c['name'],
                'type' => $c['type'],
                'nullable' => (bool) $c['nullable'],
                'default' => $c['default'],
                'value' => $values[$c['name']] ?? null,
            ])
            ->all();

        return Inertia::render('CollateralSimulationDetail', [
            'record' => [
                'id' => $collateralSimulation->id,
                'collateral_id' => $collateralSimulation->collateral_id,
                'table' => $collateralSimulation->getTable(),
            ],
            'columns' => $columns,
            'payload' => $collateralSimulation->toCbsPayload(),
            'canManage' => (bool) request()->user()?->can('collateral-simulation.manage'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('CollateralSimulationForm', [
            'record' => null,
            ...$this->references(),
        ]);
    }

    public function edit(CollateralSimulation $collateralSimulation): Response
    {
        return Inertia::render('CollateralSimulationForm', [
            'record' => $this->row($collateralSimulation),
            ...$this->references(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['collateral_id'] = filled($data['collateral_id'] ?? null)
            ? $data['collateral_id']
            : $this->nextCollateralId();
        $record = CollateralSimulation::create($this->withDefaults($data));

        ActivityLog::record("Menambah contoh agunan {$record->collateral_id}", self::LABEL, 'success', $record);

        return to_route('collateral-simulation.index')
            ->with('success', "Contoh agunan {$record->collateral_id} ditambahkan.");
    }

    public function update(Request $request, CollateralSimulation $collateralSimulation): RedirectResponse
    {
        $before = $collateralSimulation->getOriginal();
        $collateralSimulation->update($this->withDefaults($this->validated($request, $collateralSimulation)));

        ActivityLog::record(
            "Memperbarui contoh agunan {$collateralSimulation->collateral_id}",
            self::LABEL,
            'info',
            $collateralSimulation,
            ActivityLog::diffOf($collateralSimulation, $before),
        );

        return to_route('collateral-simulation.index')
            ->with('success', "Contoh agunan {$collateralSimulation->collateral_id} diperbarui.");
    }

    public function destroy(CollateralSimulation $collateralSimulation): RedirectResponse
    {
        $id = $collateralSimulation->collateral_id;
        $collateralSimulation->delete();

        ActivityLog::record("Menghapus contoh agunan {$id}", self::LABEL, 'warning');

        return back()->with('success', "Contoh agunan {$id} dihapus.");
    }

    /** Agunan ID diisi sistem bila pengguna membiarkannya kosong. */
    private function nextCollateralId(): string
    {
        $next = CollateralSimulation::max('id') + 1;

        while (CollateralSimulation::where('collateral_id', $id = 'AGN-'.str_pad((string) $next, 6, '0', STR_PAD_LEFT))->exists()) {
            $next++;
        }

        return $id;
    }

    /** Kolom yang tak boleh null di basis data diberi nilai bawaan CBS. */
    private function withDefaults(array $data): array
    {
        // Semua teks yang diketik pengguna disimpan HURUF BESAR (mengikuti CBS).
        foreach (['collateral_id', 'document_number', 'description', 'owner_name', 'owner_address', 'appraiser_name', 'independent_name'] as $key) {
            if (filled($data[$key] ?? null)) {
                $data[$key] = mb_strtoupper($data[$key]);
            }
        }

        foreach (['guarantee_value', 'adjustment_value', 'fair_value', 'njop_value', 'appraisal_value', 'independent_value'] as $key) {
            $data[$key] = (int) ($data[$key] ?? 0);
        }

        return [
            ...$data,
            'insurance_code' => ($data['insurance_code'] ?? '') ?: 'T',
            'ppap_code' => ($data['ppap_code'] ?? '') ?: '1',
        ];
    }

    private function validated(Request $request, ?CollateralSimulation $current = null): array
    {
        // Saat menyunting (tahap analisa) kondisi, asuransi, dan taksasi wajib diisi.
        $onEdit = $current ? ['required'] : ['nullable'];

        return $request->validate([
            'collateral_id' => [
                'nullable', 'string', 'max:50',
                Rule::unique('collateral_simulations', 'collateral_id')->ignore($current?->id),
            ],
            'collateral_type_code' => ['required', 'string', 'exists:collateral_types,code'],
            'binding_type_code' => ['nullable', 'string', 'exists:binding_types,code'],
            'document_number' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:255'],
            'owner_name' => ['required', 'string', 'max:100'],
            'owner_address' => ['required', 'string', 'max:255'],
            'region_code' => ['required', 'string', 'max:8'],
            'region_label' => ['nullable', 'string', 'max:150'],
            'guarantee_value' => ['nullable', 'integer', 'min:0'],
            'adjustment_value' => ['nullable', 'integer', 'min:0'],
            'fair_value' => ['nullable', 'integer', 'min:0'],
            'njop_value' => ['nullable', 'integer', 'min:0'],
            'appraisal_value' => ['nullable', 'integer', 'min:0'],
            'independent_value' => ['nullable', 'integer', 'min:0'],
            'appraiser_name' => ['nullable', 'string', 'max:100'],
            'appraised_at' => [...$onEdit, 'date'],
            'independent_name' => ['nullable', 'string', 'max:100'],
            'independent_at' => ['nullable', 'date'],
            'condition_code' => [...$onEdit, 'string', 'exists:collateral_conditions,code'],
            'condition_date' => [...$onEdit, 'date'],
            'insurance_code' => [...$onEdit, 'in:Y,T'],
            'ppap_code' => ['nullable', 'string', 'exists:collateral_methods,code'],
            'insurance_date' => [...$onEdit, 'date'],
        ], [], [
            'collateral_id' => 'agunan id',
            'collateral_type_code' => 'jenis agunan',
            'owner_name' => 'nama pemilik',
            'owner_address' => 'alamat agunan',
            'region_code' => 'lokasi agunan',
            'binding_type_code' => 'jenis pengikatan',
            'ppap_code' => 'metode hitung',
            'document_number' => 'no. dokumen',
            'description' => 'keterangan agunan',
            'condition_code' => 'kondisi',
            'condition_date' => 'tgl kondisi',
            'insurance_code' => 'diasuransikan',
            'insurance_date' => 'tgl asuransi',
            'appraised_at' => 'tgl taksasi',
        ]);
    }

    /** Referensi untuk formulir. */
    private function references(): array
    {
        return [
            'collateralTypes' => CollateralType::orderBy('code')
                ->get(['code', 'name'])
                ->map(fn ($t) => ['value' => $t->code, 'label' => "{$t->code} : {$t->name}"])->all(),
            'bindingTypes' => BindingType::orderBy('code')
                ->get(['code', 'name'])
                ->map(fn ($t) => ['value' => $t->code, 'label' => "{$t->code} : {$t->name}"])->all(),
            'conditions' => CollateralCondition::orderBy('code')
                ->get(['code', 'name'])
                ->map(fn ($t) => ['value' => $t->code, 'label' => "{$t->code} : {$t->name}"])->all(),
            'methods' => CollateralMethod::orderBy('code')
                ->get(['code', 'name'])
                ->map(fn ($t) => ['value' => $t->code, 'label' => "{$t->code} : {$t->name}"])->all(),
            'regionOptions' => Region::query()
                ->select('code', 'regency')
                ->distinct()
                ->orderBy('code')
                ->get()
                ->map(fn (Region $r) => ['value' => $r->code, 'label' => "{$r->code} : {$r->regency}"])
                ->all(),
        ];
    }

    private function row(CollateralSimulation $r): array
    {
        return [
            ...$r->only([
                'id', 'collateral_id', 'collateral_type_code',
                'binding_type_code', 'securities_rank', 'rating_agency', 'document_number',
                'description', 'owner_name', 'owner_address', 'region_code',
                'region_label', 'guarantee_value', 'adjustment_value', 'fair_value', 'njop_value',
                'appraisal_value', 'independent_value', 'appraiser_name', 'independent_name',
                'condition_code', 'insurance_code', 'ppap_code',
            ]),
            'appraised_at' => $r->appraised_at?->format('Y-m-d'),
            'independent_at' => $r->independent_at?->format('Y-m-d'),
            'condition_date' => $r->condition_date?->format('Y-m-d'),
            'insurance_date' => $r->insurance_date?->format('Y-m-d'),
            'type_label' => CollateralType::where('code', $r->collateral_type_code)->value('name'),
            'binding_label' => $r->binding_type_code
                ? BindingType::where('code', $r->binding_type_code)->value('name')
                : null,
            'condition_label' => $r->condition_code
                ? CollateralCondition::where('code', $r->condition_code)->value('name')
                : null,
            'method_label' => $r->ppap_code
                ? CollateralMethod::where('code', $r->ppap_code)->value('name')
                : null,
            'created_at' => $r->created_at?->translatedFormat('d M Y H:i'),
            'updated_at' => $r->updated_at?->translatedFormat('d M Y H:i'),
        ];
    }
}
