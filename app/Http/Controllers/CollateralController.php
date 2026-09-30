<?php

namespace App\Http\Controllers;

use App\Models\BindingType;
use App\Models\Collateral;
use App\Models\CollateralCondition;
use App\Models\CollateralMethod;
use App\Models\CollateralType;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CollateralController extends Controller
{
    private const SORTABLE = ['cbs_id', 'collateral_type_code', 'owner_name', 'appraisal_value', 'created_at'];

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
        return Inertia::render('collaterals/form', [
            'collateral' => [
                ...$collateral->only(['id', 'cbs_id', 'credit_account', 'collateral_type_code', 'binding_type_code', 'document_number', 'description', 'owner_name', 'owner_address', 'region_code', 'region_label', 'guarantee_value', 'adjustment_value', 'fair_value', 'njop_value', 'appraisal_value', 'independent_value', 'appraiser_name', 'independent_name', 'condition_code', 'insurance_code', 'ppap_code']),
                'appraised_at' => $collateral->appraised_at?->toDateString(),
                'independent_at' => $collateral->independent_at?->toDateString(),
                'condition_date' => $collateral->condition_date?->toDateString(),
                'insurance_date' => $collateral->insurance_date?->toDateString(),
            ],
            'options' => $this->options(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $collateral = Collateral::create([...$this->normalized($this->validated($request)), 'created_by' => $request->user()->id]);

        return to_route('collaterals.index')->with('success', 'Collateral '.($collateral->cbs_id ?? "#{$collateral->id}").' created.');
    }

    public function update(Request $request, Collateral $collateral): RedirectResponse
    {
        $data = $this->normalized($this->validated($request, $collateral));
        // When a collateral is edited (appraisal) the appraiser and appraisal date are recorded as whoever saves.
        $data['appraiser_name'] = mb_strtoupper($request->user()->name);
        $data['appraised_at'] = now()->toDateString();

        $collateral->update($data);

        return to_route('collaterals.index')->with('success', 'Collateral '.($collateral->cbs_id ?? "#{$collateral->id}").' updated.');
    }

    public function destroy(Collateral $collateral): RedirectResponse
    {
        if (($count = $collateral->loanApplications()->count()) > 0) {
            return back()->with('error', "This collateral is attached to {$count} loan ".str('application')->plural($count).' and cannot be deleted. Detach it first.');
        }

        $collateral->delete();

        return back()->with('success', 'Collateral deleted.');
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
        ], [], [
            'cbs_id' => 'collateral ID', 'credit_account' => 'credit account', 'collateral_type_code' => 'collateral type',
            'binding_type_code' => 'binding type', 'document_number' => 'document number', 'owner_name' => 'owner name',
            'owner_address' => 'collateral address', 'region_code' => 'location', 'condition_code' => 'condition',
            'condition_date' => 'condition date', 'insurance_code' => 'insured', 'insurance_date' => 'insurance date',
            'ppap_code' => 'valuation method', 'description' => 'description',
        ]);
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
            'regions' => Region::query()->select('code', 'regency')->distinct()->orderBy('code')->get()
                ->map(fn (Region $r): array => ['value' => $r->code, 'label' => "{$r->code} : {$r->regency}"]),
        ];
    }
}
