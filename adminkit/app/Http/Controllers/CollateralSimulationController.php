<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\BindingType;
use App\Models\CollateralCondition;
use App\Models\CollateralMethod;
use App\Models\CollateralSimulation;
use App\Models\CollateralType;
use App\Models\OwnershipStatus;
use App\Models\Region;
use App\Support\TableQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    /** Kolom yang tak boleh null di basis data diberi nilai bawaan CBS. */
    private function withDefaults(array $data): array
    {
        foreach (['value_guarantee', 'value_adjustment', 'value_fair', 'value_njop', 'value_appraisal', 'value_independent'] as $key) {
            $data[$key] = (int) ($data[$key] ?? 0);
        }

        return [
            ...$data,
            'paripasu' => (int) ($data['paripasu'] ?? 0),
            'insured' => ($data['insured'] ?? '') ?: 'T',
            'ppap_code' => ($data['ppap_code'] ?? '') ?: '1',
        ];
    }

    private function validated(Request $request, ?CollateralSimulation $current = null): array
    {
        return $request->validate([
            'collateral_id' => ['nullable', 'string', 'max:50'],
            'paripasu' => ['nullable', 'integer', 'min:0', 'max:100'],
            'file_number' => ['nullable', 'string', 'max:50'],
            'auto_number' => ['boolean'],
            'collateral_type_code' => ['required', 'string', 'exists:collateral_types,code'],
            'binding_type_code' => ['nullable', 'string', 'exists:binding_types,code'],
            'ownership' => ['nullable', 'string', 'max:100'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'owner_name' => ['required', 'string', 'max:100'],
            'owner_address' => ['required', 'string', 'max:255'],
            'owner_same_as_cif' => ['boolean'],
            'region_code' => ['required', 'string', 'max:8'],
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'region_label' => ['nullable', 'string', 'max:150'],
            'value_guarantee' => ['nullable', 'integer', 'min:0'],
            'value_adjustment' => ['nullable', 'integer', 'min:0'],
            'value_fair' => ['nullable', 'integer', 'min:0'],
            'value_njop' => ['nullable', 'integer', 'min:0'],
            'value_appraisal' => ['nullable', 'integer', 'min:0'],
            'value_independent' => ['nullable', 'integer', 'min:0'],
            'appraiser_name' => ['nullable', 'string', 'max:100'],
            'appraised_at' => ['nullable', 'date'],
            'independent_appraiser_name' => ['nullable', 'string', 'max:100'],
            'independent_appraised_at' => ['nullable', 'date'],
            'condition_code' => ['nullable', 'string', 'exists:collateral_conditions,code'],
            'condition_date' => ['nullable', 'date'],
            'insured' => ['nullable', 'in:Y,T'],
            'ppap_code' => ['nullable', 'string', 'exists:collateral_methods,code'],
            'insurance_start_date' => ['nullable', 'date'],
        ], [], [
            'collateral_id' => 'agunan id',
            'collateral_type_code' => 'jenis agunan',
            'owner_name' => 'nama pemilik',
            'owner_address' => 'alamat agunan',
            'region_code' => 'lokasi agunan',
            'binding_type_code' => 'jenis pengikatan',
            'ppap_code' => 'metode hitung',
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
            'ownershipStatuses' => OwnershipStatus::orderBy('code')
                ->get(['collateral_type_code', 'code', 'name'])
                ->groupBy('collateral_type_code')
                ->map(fn ($rows) => $rows->map(fn ($r) => ['value' => $r->name, 'label' => $r->name])->all()),
        ];
    }

    private function row(CollateralSimulation $r): array
    {
        return [
            ...$r->only([
                'id', 'collateral_id', 'paripasu', 'file_number', 'auto_number', 'collateral_type_code',
                'binding_type_code', 'securities_rank', 'rating_agency', 'ownership', 'document_number',
                'description', 'owner_name', 'owner_address', 'owner_same_as_cif', 'region_code',
                'region_label', 'value_guarantee', 'value_adjustment', 'value_fair', 'value_njop',
                'value_appraisal', 'value_independent', 'appraiser_name', 'independent_appraiser_name',
                'condition_code', 'insured', 'ppap_code', 'region_id',
            ]),
            'region' => $r->region_id
                ? Region::where('id', $r->region_id)->first(['id', 'regency', 'district', 'village'])
                : null,
            'appraised_at' => $r->appraised_at?->format('Y-m-d'),
            'independent_appraised_at' => $r->independent_appraised_at?->format('Y-m-d'),
            'condition_date' => $r->condition_date?->format('Y-m-d'),
            'insurance_start_date' => $r->insurance_start_date?->format('Y-m-d'),
            'type_label' => CollateralType::where('code', $r->collateral_type_code)->value('name'),
            'binding_label' => $r->binding_type_code
                ? BindingType::where('code', $r->binding_type_code)->value('name')
                : null,
            'payload' => $r->toCbsPayload(),
        ];
    }
}
