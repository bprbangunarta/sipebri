<?php

namespace App\Http\Controllers;

use App\Audit\Audit;
use App\Enums\LoanStatus;
use App\Enums\RoleName;
use App\Models\BindingType;
use App\Models\Collateral;
use App\Models\CollateralType;
use App\Models\CommitteePath;
use App\Models\Installment;
use App\Models\Institution;
use App\Models\LoanApplication;
use App\Models\Method;
use App\Models\Office;
use App\Models\Product;
use App\Models\ProductParameter;
use App\Models\Region;
use App\Models\User;
use App\Support\CustomerDirectory;
use App\Support\LendingLimit;
use App\Support\Notify;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Loan applications, stage 1 of the credit flow. The applicant's identity is NOT stored: it is looked
 * up from the customer master by national ID (NIK); the file keeps only NIK, name and CIF number.
 */
class LoanApplicationController extends Controller
{
    private const PER_PAGE_OPTIONS = [10, 25, 50];

    private const SORTABLE = ['application_code', 'application_date', 'full_name', 'requested_amount', 'status'];

    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', Rule::enum(LoanStatus::class)], 'product' => ['nullable', 'integer'],
            'sort' => ['nullable', 'string'], 'direction' => ['nullable', 'in:asc,desc'], 'per_page' => ['nullable', 'integer'],
        ]);
        $sort = in_array($filters['sort'] ?? null, self::SORTABLE, true) ? $filters['sort'] : 'application_code';
        $direction = $filters['direction'] ?? 'desc';

        $loans = LoanApplication::query()
            ->with(['product:id,alias,name', 'office:id,alias'])
            ->where('created_by', $request->user()->id)
            ->when($filters['status'] ?? null, fn ($q, string $status) => $q->where('status', $status))
            ->when($filters['product'] ?? null, fn ($q, int $id) => $q->where('product_id', $id))
            ->when($filters['search'] ?? null, function ($q, string $term) {
                $like = '%'.addcslashes($term, '%_\\').'%';
                $q->where(fn ($w) => $w->where('application_code', 'like', $like)->orWhere('full_name', 'like', $like)->orWhere('nik', 'like', $like));
            })
            ->orderBy($sort, $direction)->orderBy('id', 'desc')
            ->paginate(in_array((int) ($filters['per_page'] ?? 0), self::PER_PAGE_OPTIONS, true) ? (int) $filters['per_page'] : 10)
            ->withQueryString();

        return Inertia::render('loan-applications/index', [
            'loans' => $loans->through(fn (LoanApplication $l): array => $this->row($l)),
            'filters' => ['search' => $filters['search'] ?? '', 'status' => $filters['status'] ?? null, 'product' => $filters['product'] ?? null, 'sort' => $sort, 'direction' => $direction, 'per_page' => $loans->perPage()],
            'perPageOptions' => self::PER_PAGE_OPTIONS,
            'statuses' => LoanStatus::options(),
            'products' => Product::orderBy('code')->get(['id', 'alias', 'name'])->map(fn (Product $p): array => ['value' => $p->id, 'label' => "{$p->alias} : {$p->name}"]),
            'canManage' => $request->user()->can('loan-applications.manage'),
            'customerSource' => 'the customer master (Codex)',
        ]);
    }

    /** Applicant identity from the customer master. */
    public function lookup(Request $request): JsonResponse
    {
        $nik = (string) $request->query('nik', '');

        try {
            $customer = CustomerDirectory::find($nik);
        } catch (Throwable $e) {
            report($e);

            return response()->json(['found' => false, 'customer' => null, 'message' => 'The customer system cannot be reached right now. Please try again shortly.'], 503);
        }

        // The national ID is personal data: the trail keeps only its last four digits.
        Audit::record('customers.lookup', 'customers', 'lookup', context: ['nik' => str_repeat('*', max(0, strlen($nik) - 4)).substr($nik, -4), 'found' => (bool) $customer], label: 'Customer lookup');

        return response()->json([
            'found' => (bool) $customer,
            'customer' => $customer,
            'message' => $customer ? null : 'This national ID is not registered in the customer system. Register the customer first.',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['nik' => ['required', 'digits:16']], [], ['nik' => 'NIK']);

        try {
            $customer = CustomerDirectory::find($data['nik']);
        } catch (Throwable $e) {
            report($e);

            throw ValidationException::withMessages(['nik' => 'The customer system cannot be reached right now. Please try again shortly.']);
        }

        if (! $customer) {
            throw ValidationException::withMessages(['nik' => 'This national ID is not registered in the customer system, so the application cannot continue.']);
        }

        $loan = LoanApplication::create([
            'application_code' => LoanApplication::nextCode(),
            'application_date' => now()->toDateString(),
            'status' => LoanStatus::Draft,
            'nik' => $data['nik'],
            'full_name' => $customer['full_name'],
            'cif_number' => $customer['cif_number'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        return to_route('loan-applications.show', $loan)->with('success', "Application {$loan->application_code} opened. Complete the steps below.");
    }

    public function show(Request $request, LoanApplication $loanApplication): Response
    {
        $this->authorize('view', $loanApplication);
        $loan = $loanApplication->load(['collaterals', 'product:id,alias,name', 'office:id,alias']);
        Audit::record('loan_applications.viewed', 'loan_applications', 'viewed', $loanApplication);

        return Inertia::render('loan-applications/show', [
            'loan' => [
                ...$this->row($loan),
                ...$loan->only(['institution_id', 'marketing', 'committee_path_id', 'usage_type', 'method_id', 'installment_id', 'interest_rate', 'cif_number', 'supervisor_id', 'note']),
                'checklist' => $this->checklist($loan),
                'collateral_required' => $this->collateralRequired($loan),
            ],
            'collaterals' => $loan->collaterals->map(fn (Collateral $c): array => [...$c->only(['id', 'cbs_id', 'collateral_type_code', 'owner_name', 'document_number', 'description']), 'appraisal_value' => $c->appraisal_value]),
            'collateralOptions' => $this->collateralOptions(),
            'editable' => $request->user()->can('modify', $loan),
            'references' => $this->references(),
        ]);
    }

    /** Step "Application data". */
    public function update(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        $this->authorize('modify', $loanApplication);

        $parameter = ProductParameter::where('product_id', (int) $request->input('product_id'))->first();
        $bmpk = LendingLimit::bmpk();
        $productMax = $parameter?->max_amount ? (int) $parameter->max_amount : null;
        $ceiling = $this->amountCeiling($productMax, $bmpk);

        $data = $request->validate([
            'application_date' => ['required', 'date'],
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')->where('is_active', true)],
            'committee_path_id' => ['required', 'integer', Rule::exists('committee_paths', 'id')->where('is_active', true)],
            'requested_amount' => ['required', 'integer', 'min:'.max(1, (int) $parameter?->min_amount), ...($ceiling ? ['max:'.$ceiling] : [])],
            'requested_tenor' => ['required', 'integer', 'min:'.max(1, (int) $parameter?->min_tenor), 'max:'.($parameter?->max_tenor ?: 600)],
            'method_id' => ['required', 'integer', 'exists:methods,id', ...($parameter?->allowed_method_ids ? [Rule::in($parameter->allowed_method_ids)] : [])],
            'installment_id' => ['required', 'integer', 'exists:installments,id', ...($parameter?->allowed_installment_ids ? [Rule::in($parameter->allowed_installment_ids)] : [])],
            'interest_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'usage_type' => ['required', Rule::in(LoanApplication::USAGE_TYPES)],
            'office_id' => ['required', 'integer', 'exists:offices,id'],
            'supervisor_id' => ['required', 'integer', fn (string $attribute, mixed $value, \Closure $fail) => User::role(RoleName::AnalysisSectionHead->value)->whereKey($value)->exists() ? null : $fail('The selected section head does not hold the analysis section head role.')],
            'institution_id' => ['nullable', 'integer', 'exists:institutions,id'],
            'marketing' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:255'],
        ], $this->parameterMessages($parameter, $productMax, $bmpk), [
            'product_id' => 'product', 'committee_path_id' => 'category', 'requested_amount' => 'loan amount', 'requested_tenor' => 'tenor',
            'method_id' => 'interest method', 'installment_id' => 'installment system', 'interest_rate' => 'interest rate', 'usage_type' => 'usage',
            'office_id' => 'office', 'supervisor_id' => 'section head',
        ]);

        // The category must be a committee path of this product or one that applies to every product.
        $path = CommitteePath::query()->findOrFail((int) $data['committee_path_id']);
        if ($path->product_id !== null && $path->product_id !== (int) $data['product_id']) {
            throw ValidationException::withMessages(['committee_path_id' => 'This category does not belong to the selected product.']);
        }

        if (filled($data['marketing'] ?? null)) {
            $data['marketing'] = mb_strtoupper($data['marketing']);
        }

        $loanApplication->update($data);
        $warning = $this->tenorWarning($loanApplication);

        return back()->with('success', 'Application data saved.')->with('warning', $warning);
    }

    public function attachCollateral(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        $this->authorize('modify', $loanApplication);
        $data = $request->validate(['collateral_id' => ['required', 'integer', 'exists:collaterals,id']], [], ['collateral_id' => 'collateral']);

        $loanApplication->collaterals()->syncWithoutDetaching([$data['collateral_id']]);
        Audit::record('loan_applications.collateral_attached', 'loan_applications', 'collateral_attached', $loanApplication, new: ['collateral_id' => (int) $data['collateral_id']]);

        return back()->with('success', 'Collateral attached.');
    }

    /** A collateral created straight from the file and attached to it. */
    public function storeCollateral(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        $this->authorize('modify', $loanApplication);

        $data = $request->validate([
            'collateral_type_code' => ['required', 'string', 'exists:collateral_types,code'],
            'binding_type_code' => ['nullable', 'string', 'exists:binding_types,code'],
            'document_number' => ['required', 'string', 'max:100'],
            'owner_name' => ['required', 'string', 'max:100'],
            'owner_address' => ['required', 'string', 'max:255'],
            'region_code' => ['required', 'string', 'max:8', 'exists:regions,code'],
            'description' => ['required', 'string', 'max:255'],
        ], [], ['collateral_type_code' => 'collateral type', 'document_number' => 'document number', 'owner_name' => 'owner name', 'owner_address' => 'collateral address', 'region_code' => 'location']);

        foreach (['document_number', 'owner_name', 'owner_address', 'description'] as $key) {
            $data[$key] = mb_strtoupper($data[$key]);
        }

        $collateral = Collateral::create([...$data, 'created_by' => $request->user()->id]);
        $loanApplication->collaterals()->syncWithoutDetaching([$collateral->id]);
        Audit::record('loan_applications.collateral_attached', 'loan_applications', 'collateral_attached', $loanApplication, new: ['collateral_id' => $collateral->id], context: ['created_with_file' => true]);

        return back()->with('success', 'New collateral added to the file.');
    }

    public function detachCollateral(LoanApplication $loanApplication, Collateral $collateral): RedirectResponse
    {
        $this->authorize('modify', $loanApplication);
        $loanApplication->collaterals()->detach($collateral->id);
        Audit::record('loan_applications.collateral_detached', 'loan_applications', 'collateral_detached', $loanApplication, ['collateral_id' => $collateral->id]);

        return back()->with('success', 'Collateral detached.');
    }

    /** Submit a draft once the application data and collateral are complete. */
    public function confirm(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        $this->authorize('modify', $loanApplication);

        if (in_array(false, $this->checklist($loanApplication), true)) {
            return back()->with('error', 'Complete the customer, application data and collateral steps before submitting.');
        }

        $loanApplication->update(['status' => LoanStatus::Submitted]);

        Notify::toPermission('scheduling.manage', 'New application awaiting scheduling', 'Loan applications',
            "File {$loanApplication->application_code} ({$loanApplication->full_name}) is ready for a survey schedule.", '/scheduling');

        return back()->with('success', 'Application submitted.');
    }

    public function destroy(LoanApplication $loanApplication): RedirectResponse
    {
        $this->authorize('modify', $loanApplication);

        $code = $loanApplication->application_code;
        $loanApplication->delete();

        return to_route('loan-applications.index')->with('success', "Application {$code} deleted.");
    }

    /**
     * Completeness of each step, shown on the submit card.
     *
     * @return array{customer: bool, application: bool, collateral: bool}
     */
    private function checklist(LoanApplication $loan): array
    {
        try {
            $customer = (bool) CustomerDirectory::find($loan->nik);
        } catch (Throwable $e) {
            report($e);
            $customer = false;
        }

        return [
            'customer' => $customer,
            'application' => (bool) $loan->product_id && (bool) $loan->committee_path_id && (bool) $loan->office_id
                && (bool) $loan->supervisor_id && $loan->requested_amount > 0 && $loan->requested_tenor > 0,
            'collateral' => ! $this->collateralRequired($loan) || $loan->collaterals()->exists(),
        ];
    }

    /**
     * The tenor should be a multiple of the installment period (e.g. seasonal = 6 months) so the
     * principal instalment stays a whole number. A warning only.
     */
    private function tenorWarning(LoanApplication $loan): ?string
    {
        $period = $loan->installment_id === null ? 0 : $loan->installment->period_months;
        $tenor = (int) $loan->requested_tenor;

        if ($period < 2 || $tenor < 1 || $tenor % $period === 0) {
            return null;
        }

        return "A tenor of {$tenor} months is not a multiple of {$period} months for the {$loan->installment->name} installment system. The principal instalment may not be a whole number.";
    }

    /**
     * @return array<string, string>
     */
    private function parameterMessages(?ProductParameter $p, ?int $productMax, ?int $bmpk): array
    {
        $idr = LendingLimit::format(...);
        // The BMPK message wins when it is the stricter of the two limits.
        $max = $bmpk !== null && ($productMax === null || $bmpk <= $productMax)
            ? ['requested_amount.max' => "The loan amount exceeds the legal lending limit (BMPK) of {$idr($bmpk)}."]
            : ($productMax !== null ? ['requested_amount.max' => "The maximum loan amount is {$idr($productMax)} for this product."] : []);

        if (! $p) {
            return $max;
        }

        return [
            ...$max,
            'requested_amount.min' => "The minimum loan amount is {$idr($p->min_amount)} for this product.",
            'requested_tenor.min' => "The minimum tenor is {$p->min_tenor} months for this product.",
            'requested_tenor.max' => "The maximum tenor is {$p->max_tenor} months for this product.",
            'method_id.in' => 'This interest method is not allowed for the product.',
            'installment_id.in' => 'This installment system is not allowed for the product.',
        ];
    }

    /**
     * Collaterals that can be attached. The label is the identity (ID and owner); the second line carries what tells
     * similar collaterals apart: document number, type, appraisal and the full description (nothing is cut off).
     *
     * @return list<array{value: int, label: string, description: string}>
     */
    private function collateralOptions(): array
    {
        $types = CollateralType::query()->pluck('name', 'code');

        $options = Collateral::query()->orderBy('id')->get()
            ->map(fn (Collateral $c): array => [
                'value' => $c->id,
                'label' => trim(($c->cbs_id ?? "#{$c->id}").' — '.$c->owner_name),
                'description' => collect([
                    $c->document_number ? 'Doc '.$c->document_number : null,
                    $types[$c->collateral_type_code] ?? $c->collateral_type_code,
                    $c->appraisal_value ? 'Appraisal '.LendingLimit::format($c->appraisal_value) : null,
                    $c->description,
                ])->filter()->implode(' · '),
            ]);

        return array_values($options->all());
    }

    /** The strictest of the product maximum and the BMPK (null = no limit). */
    private function amountCeiling(?int $productMax, ?int $bmpk): ?int
    {
        $limits = array_filter([$productMax, $bmpk]);

        return $limits === [] ? null : min($limits);
    }

    private function collateralRequired(LoanApplication $loan): bool
    {
        return $loan->product_id !== null && (bool) ProductParameter::where('product_id', $loan->product_id)->value('collateral_required');
    }

    /**
     * @return array<string, mixed>
     */
    private function references(): array
    {
        $code = fn ($model, array $cols = ['code', 'name']) => $model::orderBy('code')->get()->map(fn ($m): array => ['value' => $m->getKey(), 'label' => "{$m->code} : {$m->name}"]);

        return [
            'usageTypes' => array_map(fn (string $v): array => ['value' => $v, 'label' => $v], LoanApplication::USAGE_TYPES),
            'offices' => Office::orderBy('code')->get()->map(fn (Office $o): array => ['value' => $o->id, 'label' => "{$o->alias} : {$o->name}"]),
            'products' => Product::where('is_active', true)->orderBy('code')->get()->map(fn (Product $p): array => ['value' => $p->id, 'label' => "{$p->alias} : {$p->name}"]),
            'institutions' => $code(Institution::class),
            'methods' => $code(Method::class),
            'installments' => Installment::orderBy('code')->get()->map(fn (Installment $i): array => ['value' => $i->id, 'label' => "{$i->code} : {$i->name}", 'period_months' => $i->period_months]),
            'supervisors' => User::role(RoleName::AnalysisSectionHead->value)->orderBy('name')->get(['id', 'name'])->map(fn (User $u): array => ['value' => $u->id, 'label' => $u->name]),
            'collateralTypes' => CollateralType::orderBy('code')->get()->map(fn ($t): array => ['value' => $t->code, 'label' => "{$t->code} : {$t->name}"]),
            'bindingTypes' => BindingType::orderBy('code')->get()->map(fn ($t): array => ['value' => $t->code, 'label' => "{$t->code} : {$t->name}"]),
            'regions' => Region::query()->select('code', 'regency')->distinct()->orderBy('code')->get()->map(fn (Region $r): array => ['value' => $r->code, 'label' => "{$r->code} : {$r->regency}"]),
            'parameters' => ProductParameter::all()->keyBy('product_id')->map(fn (ProductParameter $p): array => [
                'method_ids' => (array) $p->allowed_method_ids, 'installment_ids' => (array) $p->allowed_installment_ids,
                'default_method_id' => $p->default_method_id, 'default_installment_id' => $p->default_installment_id, 'interest_rate' => $p->interest_rate,
                'min_amount' => (int) $p->min_amount, 'max_amount' => (int) ($this->amountCeiling($p->max_amount ? (int) $p->max_amount : null, LendingLimit::bmpk()) ?? 0), 'min_tenor' => (int) $p->min_tenor, 'max_tenor' => (int) $p->max_tenor,
                'collateral_required' => $p->collateral_required,
            ]),
            'categories' => CommitteePath::where('is_active', true)->orderBy('condition')->get(['id', 'product_id', 'condition'])
                ->groupBy(fn (CommitteePath $p): string => (string) ($p->product_id ?? 'global'))
                ->map(fn ($paths) => $paths->map(fn (CommitteePath $p): array => ['value' => $p->id, 'label' => $p->condition ?: 'Normal'])->values()),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function row(LoanApplication $loan): array
    {
        return [
            ...$loan->only(['id', 'application_code', 'nik', 'full_name', 'office_id', 'product_id', 'requested_amount', 'requested_tenor']),
            'status' => $loan->status->value,
            'status_label' => $loan->status->label(),
            'status_tone' => $loan->status->tone(),
            'application_date' => $loan->application_date->toDateString(),
            'product_label' => $loan->product ? "{$loan->product->alias} : {$loan->product->name}" : null,
            'office_label' => $loan->office?->alias,
        ];
    }
}
