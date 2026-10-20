<?php

namespace App\Http\Controllers;

use App\Audit\Audit;
use App\Models\BindingType;
use App\Models\Collateral;
use App\Models\CollateralCondition;
use App\Models\CollateralMethod;
use App\Models\CollateralType;
use App\Models\Region;
use App\Support\Appraisal\LandBuildingTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CollateralController extends Controller
{
    private const SORTABLE = ['cbs_id', 'collateral_type_code', 'owner_name', 'description', 'document_number', 'appraisal_value', 'created_at'];

    private const PER_PAGE_OPTIONS = [10, 25, 50];

    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'], 'type' => ['nullable', 'string'],
            'sort' => ['nullable', 'string'], 'direction' => ['nullable', 'in:asc,desc'], 'per_page' => ['nullable', 'integer'],
        ]);
        $sort = in_array($filters['sort'] ?? null, self::SORTABLE, true) ? $filters['sort'] : 'created_at';
        $direction = $filters['direction'] ?? 'desc';

        $collaterals = Collateral::query()
            ->when($filters['type'] ?? null, fn ($q, string $type) => $q->where('collateral_type_code', $type))
            ->when($filters['search'] ?? null, function ($q, string $term) {
                $like = '%'.addcslashes($term, '%_\\').'%';
                $q->where(fn ($w) => $w->where('cbs_id', 'like', $like)->orWhere('owner_name', 'like', $like)
                    ->orWhere('document_number', 'like', $like)->orWhere('description', 'like', $like));
            })
            ->orderBy($sort, $direction)->orderBy('id')
            ->paginate(in_array((int) ($filters['per_page'] ?? 0), self::PER_PAGE_OPTIONS, true) ? (int) $filters['per_page'] : 10)
            ->withQueryString();

        $types = CollateralType::pluck('name', 'code');

        return Inertia::render('collaterals/index', [
            'collaterals' => $collaterals->through(fn (Collateral $c): array => [
                ...$c->only(['id', 'cbs_id', 'collateral_type_code', 'owner_name', 'document_number', 'description', 'appraisal_value']),
                'type_label' => $types[$c->collateral_type_code] ?? $c->collateral_type_code,
            ]),
            'filters' => ['search' => $filters['search'] ?? '', 'type' => $filters['type'] ?? null, 'sort' => $sort, 'direction' => $direction, 'per_page' => $collaterals->perPage()],
            'perPageOptions' => self::PER_PAGE_OPTIONS,
            'typeOptions' => $types->map(fn (string $name, string $code): array => ['value' => $code, 'label' => "{$code} : {$name}"])->values(),
            'canManage' => $request->user()->can('collaterals.manage'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('collaterals/form', ['collateral' => null, 'options' => $this->options()]);
    }

    public function edit(Collateral $collateral): Response
    {
        Audit::record('collaterals.viewed', 'collaterals', 'viewed', $collateral);

        return Inertia::render('collaterals/form', [
            'collateral' => [
                ...$collateral->only(['id', 'cbs_id', 'credit_account', 'collateral_type_code', 'binding_type_code', 'document_number', 'description', 'owner_name', 'owner_address', 'region_code', 'region_label', 'guarantee_value', 'adjustment_value', 'fair_value', 'njop_value', 'appraisal_value', 'independent_value', 'appraiser_name', 'independent_name', 'condition_code', 'insurance_code', 'ppap_code']),
                'appraised_at' => $collateral->appraised_at?->toDateString(),
                'independent_at' => $collateral->independent_at?->toDateString(),
                'condition_date' => $collateral->condition_date?->toDateString(),
                'insurance_date' => $collateral->insurance_date?->toDateString(),
                ...$this->landFacts($collateral),
            ],
            'options' => $this->options(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $collateral = Collateral::create([...$this->normalized($this->validated($request)), 'created_by' => $request->user()->id]);

        return to_route('collaterals.index')->with('success', 'Jaminan '.($collateral->cbs_id ?? "#{$collateral->id}").' berhasil dibuat.');
    }

    public function update(Request $request, Collateral $collateral): RedirectResponse
    {
        $data = $this->normalized($this->validated($request, $collateral));
        // When a collateral is edited (appraisal) the appraiser and appraisal date are recorded as whoever saves.
        $data['appraiser_name'] = mb_strtoupper($request->user()->name);
        $data['appraised_at'] = now()->toDateString();

        $collateral->update($data);

        return to_route('collaterals.index')->with('success', 'Jaminan '.($collateral->cbs_id ?? "#{$collateral->id}").' berhasil diperbarui.');
    }

    public function destroy(Collateral $collateral): RedirectResponse
    {
        if (($count = $collateral->appraisals()->count()) > 0) {
            return back()->with('error', "Jaminan ini punya {$count} penilaian yang menjadi riwayat sehingga tidak bisa dihapus.");
        }

        if (($count = $collateral->loanApplications()->count()) > 0) {
            return back()->with('error', "Jaminan ini dilekatkan ke {$count} pengajuan sehingga tidak bisa dihapus. Lepaskan dulu.");
        }

        $collateral->delete();

        return back()->with('success', 'Jaminan berhasil dihapus.');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalized(array $data): array
    {
        foreach (Collateral::UPPERCASE as $key) {
            if (filled($data[$key] ?? null)) {
                $data[$key] = mb_strtoupper($data[$key]);
            }
        }

        foreach (Collateral::VALUES as $key) {
            $data[$key] = (int) ($data[$key] ?? 0);
        }

        $data['insurance_code'] = ($data['insurance_code'] ?? '') ?: 'T';
        $data['ppap_code'] = ($data['ppap_code'] ?? '') ?: '1';

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Collateral $current = null): array
    {
        // Editing is the appraisal step, where condition, insurance and appraisal become mandatory.
        $onEdit = $current ? ['required'] : ['nullable'];

        return $request->validate([
            'cbs_id' => ['nullable', 'string', 'max:50', Rule::unique('collaterals', 'cbs_id')->ignore($current)],
            'credit_account' => ['nullable', 'string', 'max:30', Rule::unique('collaterals', 'credit_account')->ignore($current)],
            'collateral_type_code' => ['required', 'string', 'exists:collateral_types,code'],
            'binding_type_code' => ['nullable', 'string', 'exists:binding_types,code'],
            'document_number' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:255'],
            'owner_name' => ['required', 'string', 'max:100'],
            'owner_address' => ['required', 'string', 'max:255'],
            'region_code' => ['required', 'string', 'max:8', 'exists:regions,code'],
            'region_label' => ['nullable', 'string', 'max:150'],
            'guarantee_value' => ['nullable', 'integer', 'min:0'],
            'adjustment_value' => ['nullable', 'integer', 'min:0'],
            'fair_value' => ['nullable', 'integer', 'min:0'],
            'njop_value' => ['nullable', 'integer', 'min:0'],
            'appraisal_value' => ['nullable', 'integer', 'min:0'],
            'independent_value' => ['nullable', 'integer', 'min:0'],
            'independent_name' => ['nullable', 'string', 'max:100'],
            'independent_at' => ['nullable', 'date'],
            'condition_code' => [...$onEdit, 'string', 'exists:collateral_conditions,code'],
            'condition_date' => [...$onEdit, 'date'],
            'insurance_code' => [...$onEdit, 'in:Y,T'],
            'insurance_date' => [...$onEdit, 'date'],
            'ppap_code' => ['nullable', 'string', 'exists:collateral_methods,code'],
            'province' => ['nullable', 'string', 'max:60'], 'postal_code' => ['nullable', 'string', 'max:8'], 'route_hint' => ['nullable', 'string', 'max:255'],
            'right_kind' => ['nullable', Rule::in(array_keys(Collateral::RIGHT_KINDS))], 'right_holder_name' => ['nullable', 'string', 'max:100'],
            'right_issued_at' => ['nullable', 'date'], 'right_expires_at' => ['nullable', 'date', 'after_or_equal:right_issued_at'],
            'survey_document' => ['nullable', 'string', 'max:100'], 'survey_document_date' => ['nullable', 'date'],
            'certificate_land_area' => ['nullable', 'integer', 'min:0', 'max:99999999'], 'land_office' => ['nullable', 'string', 'max:100'],
            'parcel_number' => ['nullable', 'string', 'max:50'], 'acquisition_origin' => ['nullable', 'string', 'max:150'], 'tax_object_number' => ['nullable', 'string', 'max:30'],
            'building_permit_number' => ['nullable', 'string', 'max:60'], 'building_permit_date' => ['nullable', 'date'],
            'operating_certificate_number' => ['nullable', 'string', 'max:60'], 'operating_certificate_date' => ['nullable', 'date'],
            'permitted_building_area' => ['nullable', 'integer', 'min:0', 'max:99999999'], 'permitted_use' => ['nullable', 'string', 'max:100'],
        ], [], [
            'cbs_id' => 'ID agunan', 'credit_account' => 'rekening kredit', 'collateral_type_code' => 'jenis agunan',
            'binding_type_code' => 'jenis pengikatan', 'document_number' => 'nomor dokumen', 'owner_name' => 'nama pemilik',
            'owner_address' => 'alamat agunan', 'region_code' => 'lokasi', 'condition_code' => 'kondisi',
            'condition_date' => 'tanggal kondisi', 'insurance_code' => 'asuransi', 'insurance_date' => 'tanggal asuransi',
            'ppap_code' => 'metode penilaian', 'description' => 'keterangan',
            'province' => 'provinsi', 'postal_code' => 'kode pos', 'route_hint' => 'patokan', 'right_kind' => 'jenis hak', 'right_holder_name' => 'pemegang hak',
            'right_issued_at' => 'tanggal terbit', 'right_expires_at' => 'tanggal berakhir hak', 'survey_document' => 'surat ukur', 'survey_document_date' => 'tanggal surat ukur',
            'certificate_land_area' => 'luas menurut sertipikat', 'land_office' => 'kantor pertanahan', 'parcel_number' => 'NIB', 'acquisition_origin' => 'asal perolehan',
            'tax_object_number' => 'NOP', 'building_permit_number' => 'nomor PBG/IMB', 'building_permit_date' => 'tanggal PBG/IMB',
            'operating_certificate_number' => 'nomor SLF', 'operating_certificate_date' => 'tanggal SLF', 'permitted_building_area' => 'luas bangunan menurut izin', 'permitted_use' => 'peruntukan',
        ]);
    }

    /**
     * The land and building facts as the form shows them (dates as plain dates).
     *
     * @return array<string, mixed>
     */
    private function landFacts(Collateral $collateral): array
    {
        $facts = $collateral->only(Collateral::LAND_FACTS);

        foreach (Collateral::LAND_DATES as $column) {
            $facts[$column] = $collateral->{$column}?->toDateString();
        }

        return $facts;
    }

    /**
     * @return array<string, mixed>
     */
    private function options(): array
    {
        $codeName = fn ($model) => $model::orderBy('code')->get(['code', 'name'])->map(fn ($m): array => ['value' => $m->code, 'label' => "{$m->code} : {$m->name}"]);

        return [
            'types' => $codeName(CollateralType::class),
            'bindings' => $codeName(BindingType::class),
            'conditions' => $codeName(CollateralCondition::class),
            'methods' => $codeName(CollateralMethod::class),
            // One entry per code: a code that the source data writes with two regencies lists both, instead of two entries with one value.
            'rightKinds' => collect(Collateral::RIGHT_KINDS)->map(fn (string $label, string $value): array => ['value' => $value, 'label' => $label])->values(),
            'landTypes' => LandBuildingTemplate::TYPES,
            'regions' => Region::query()->select('code', 'regency')->distinct()->orderBy('code')->orderBy('regency')->get()->groupBy('code')
                ->map(fn ($rows, $code): array => ['value' => (string) $code, 'label' => $code.' : '.$rows->pluck('regency')->implode(' / ')])->values(),
        ];
    }
}
