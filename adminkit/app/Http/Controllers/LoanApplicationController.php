<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\BindingType;
use App\Models\CollateralSimulation;
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
use App\Support\Notify;
use App\Support\TableQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Pengajuan Kredit — tahap 1 dari 9.
 * Data pemohon TIDAK disimpan: identitas diambil dari API sistem nasabah (Codex)
 * memakai nomor KTP. Berkas hanya menyimpan nik, nama, dan nomor CIF.
 */
class LoanApplicationController extends Controller
{
    private const LABEL = 'Pengajuan Kredit';

    public const STATUSES = ['DRAFT', 'DIAJUKAN', 'PENJADWALAN', 'SURVEY', 'ANALISA', 'KOMITE', 'DISETUJUI', 'DITOLAK', 'DIBATALKAN', 'REALISASI'];

    public const USAGE_TYPES = ['KONSUMTIF', 'MODAL USAHA', 'INVESTASI', 'LAINNYA'];

    private ?array $customerCache = null;

    public function index(Request $request): Response
    {
        $search = TableQuery::search($request);
        $sort = TableQuery::sort($request, ['application_code', 'application_date', 'full_name', 'requested_amount', 'status'], 'application_code');
        $dir = TableQuery::direction($request);
        $status = (string) $request->input('status', '');
        $productId = (int) $request->input('product_id', 0);

        $records = LoanApplication::query()
            ->with(['product:id,alias,name', 'office:id,alias,name', 'method:id,code,name'])
            ->where('created_by', $request->user()->name)
            ->when($search !== '', fn ($q) => $q->where(function ($w) use ($search) {
                foreach (['application_code', 'full_name', 'nik', 'credit_account',
                    'application_date', 'status', 'requested_amount', 'requested_tenor'] as $col) {
                    $w->orWhere($col, 'like', "%{$search}%");
                }

                $w->orWhereHas('product', fn ($p) => $p
                    ->where('alias', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%"));

                $w->orWhereHas('method', fn ($m) => $m
                    ->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%"));
            }))
            ->when(in_array($status, self::STATUSES, true), fn ($q) => $q->where('status', $status))
            ->when($productId > 0, fn ($q) => $q->where('product_id', $productId))
            ->orderBy($sort, $dir)
            ->paginate(TableQuery::perPage($request))
            ->withQueryString();

        return Inertia::render('LoanSimulation', [
            'records' => [
                'data' => collect($records->items())->map(fn (LoanApplication $r) => $this->row($r))->all(),
                'meta' => TableQuery::meta($records),
            ],
            'filters' => [
                'search' => $search, 'sort' => $sort, 'dir' => $dir,
                'status' => $status, 'product_id' => $productId ?: '',
            ],
            'statuses' => self::STATUSES,
            'productOptions' => Product::orderBy('code')->get(['id', 'alias', 'name'])
                ->map(fn (Product $p) => ['value' => $p->id, 'label' => "{$p->alias} : {$p->name}"])->all(),
            'sampleNiks' => CustomerDirectory::sampleNiks(),
        ]);
    }

    /** Identitas nasabah dari sistem lain (API Codex). */
    public function lookup(Request $request): JsonResponse
    {
        $nik = (string) $request->query('nik', '');

        try {
            $customer = CustomerDirectory::find($nik);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'found' => false,
                'customer' => null,
                'message' => 'Sistem data nasabah sedang tidak dapat dihubungi. Coba lagi beberapa saat.',
            ], 503);
        }

        return response()->json([
            'found' => (bool) $customer,
            'customer' => $customer,
            'message' => $customer
                ? null
                : 'Nomor KTP belum terdaftar di sistem data nasabah. Daftarkan nasabah terlebih dahulu.',
        ]);
    }

    public function show(LoanApplication $loanApplication): Response
    {
        $loanApplication->load(['collaterals', 'product:id,alias,name', 'office:id,alias,name']);

        return Inertia::render('LoanSimulationDetail', [
            'record' => $this->detail($loanApplication),
            'collaterals' => $loanApplication->collaterals
                ->map(fn (CollateralSimulation $c) => [
                    ...$c->only(['id', 'collateral_id', 'collateral_type_code', 'owner_name', 'document_number', 'description']),
                    'appraisal_value' => (int) $c->appraisal_value,
                ])->all(),
            'collateralOptions' => CollateralSimulation::query()
                ->orderBy('id')
                ->get(['id', 'collateral_id', 'owner_name', 'description'])
                ->map(fn ($c) => [
                    'value' => $c->id,
                    'label' => trim(($c->collateral_id ?? "#{$c->id}").' — '.($c->owner_name ?? '').' — '.mb_substr((string) $c->description, 0, 40)),
                ])->all(),
            'canManage' => (bool) request()->user()?->can('loan-simulation.manage'),
            ...$this->references(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nik' => ['required', 'string', 'digits:16'],
        ], [
            'nik.digits' => 'Kolom nomor KTP harus 16 angka.',
        ], ['nik' => 'nomor ktp']);

        try {
            $customer = CustomerDirectory::find($data['nik']);
        } catch (Throwable $e) {
            report($e);

            throw ValidationException::withMessages([
                'nik' => 'Sistem data nasabah sedang tidak dapat dihubungi. Coba lagi beberapa saat.',
            ]);
        }

        if (! $customer) {
            throw ValidationException::withMessages([
                'nik' => 'Nomor KTP belum terdaftar di sistem data nasabah. Pengajuan tidak dapat dilanjutkan.',
            ]);
        }

        $record = LoanApplication::create([
            'application_code' => LoanApplication::nextCode(),
            'application_date' => now()->toDateString(),
            'status' => 'DRAFT',
            'nik' => $data['nik'],
            'full_name' => $customer['full_name'],
            'cif_number' => $customer['cif_number'] ?? null,
        ]);

        ActivityLog::record("Menambah pengajuan {$record->application_code}", self::LABEL, 'success', $record);

        return to_route('loan-simulation.show', $record)
            ->with('success', "Pengajuan {$record->application_code} dibuka. Lengkapi tahapannya.");
    }

    /** Tahap "Data Pengajuan". */
    public function update(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        if ($locked = $this->locked($loanApplication)) {
            return $locked;
        }

        $before = $loanApplication->getOriginal();

        $data = $request->validate([
            'application_date' => ['required', 'date'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'committee_path_id' => ['required', 'integer', 'exists:committee_paths,id'],
            'requested_amount' => ['required', 'integer', 'min:1'],
            'requested_tenor' => ['required', 'integer', 'min:1', 'max:600'],
            'method_id' => ['required', 'integer', 'exists:methods,id'],
            'installment_id' => ['required', 'integer', 'exists:installments,id'],
            'interest_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'usage_type' => ['required', Rule::in(self::USAGE_TYPES)],
            'office_id' => ['required', 'integer', 'exists:offices,id'],
            'supervisor_id' => ['required', 'integer', 'exists:users,id'],
            'institution_id' => ['nullable', 'integer', 'exists:institutions,id'],
            'marketing' => ['nullable', 'string', 'max:100'],
        ], [], [
            'product_id' => 'produk',
            'committee_path_id' => 'kategori',
            'requested_amount' => 'plafon',
            'requested_tenor' => 'jk kredit',
            'method_id' => 'sistem bunga',
            'installment_id' => 'sistem cicilan',
            'interest_rate' => 'suku bunga',
            'usage_type' => 'penggunaan',
            'office_id' => 'wilayah/kantor',
            'supervisor_id' => 'kasi analis',
        ]);

        if (filled($data['marketing'] ?? null)) {
            $data['marketing'] = mb_strtoupper($data['marketing']);
        }

        $loanApplication->update($data);

        ActivityLog::record(
            "Memperbarui pengajuan {$loanApplication->application_code}",
            self::LABEL,
            'info',
            $loanApplication,
            ActivityLog::diffOf($loanApplication, $before),
        );

        return back()->with('success', 'Data pengajuan disimpan.');
    }

    public function attachCollateral(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        if ($locked = $this->locked($loanApplication)) {
            return $locked;
        }

        $data = $request->validate([
            'collateral_simulation_id' => ['required', 'integer', 'exists:collateral_simulations,id'],
        ], [], ['collateral_simulation_id' => 'agunan']);

        $loanApplication->collaterals()->syncWithoutDetaching([$data['collateral_simulation_id']]);

        return back()->with('success', 'Agunan dilekatkan ke berkas.');
    }

    /** Agunan baru dibuat langsung dari berkas lalu otomatis dilekatkan. */
    public function storeCollateral(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        if ($locked = $this->locked($loanApplication)) {
            return $locked;
        }

        $data = $request->validate([
            'collateral_type_code' => ['required', 'string', 'exists:collateral_types,code'],
            'binding_type_code' => ['nullable', 'string', 'exists:binding_types,code'],
            'document_number' => ['required', 'string', 'max:100'],
            'owner_name' => ['required', 'string', 'max:100'],
            'owner_address' => ['required', 'string', 'max:255'],
            'region_code' => ['required', 'string', 'max:8'],
            'region_label' => ['nullable', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:255'],
        ], [], [
            'collateral_type_code' => 'jenis agunan',
            'document_number' => 'no. dokumen',
            'owner_name' => 'nama pemilik',
            'owner_address' => 'alamat agunan',
            'region_code' => 'lokasi agunan',
            'description' => 'keterangan agunan',
        ]);

        foreach (['document_number', 'owner_name', 'owner_address', 'description'] as $key) {
            $data[$key] = mb_strtoupper($data[$key]);
        }

        $collateral = CollateralSimulation::create([...$data, 'insurance_code' => 'T', 'ppap_code' => '1']);
        $loanApplication->collaterals()->syncWithoutDetaching([$collateral->id]);

        ActivityLog::record(
            "Menambah agunan pada berkas {$loanApplication->application_code}",
            self::LABEL,
            'success',
            $loanApplication,
        );

        return back()->with('success', 'Agunan baru ditambahkan ke berkas.');
    }

    public function detachCollateral(LoanApplication $loanApplication, CollateralSimulation $collateral): RedirectResponse
    {
        if ($locked = $this->locked($loanApplication)) {
            return $locked;
        }

        $loanApplication->collaterals()->detach($collateral->id);

        return back()->with('success', 'Agunan dilepas dari berkas.');
    }

    /** Berkas DRAFT diajukan setelah data pengajuan & agunan lengkap. */
    public function confirm(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        if ($loanApplication->status !== 'DRAFT') {
            return back()->with('error', 'Berkas sudah diajukan.');
        }

        if (in_array(false, $this->checklist($loanApplication), true)) {
            return back()->with('error', 'Lengkapi data pengajuan dan agunan sebelum diajukan.');
        }

        $loanApplication->update(['status' => 'DIAJUKAN']);

        ActivityLog::record("Mengajukan berkas {$loanApplication->application_code}", self::LABEL, 'success', $loanApplication);

        Notify::toPermission(
            'scheduling-simulation.manage',
            'Pengajuan baru menunggu penjadwalan',
            self::LABEL,
            "Berkas {$loanApplication->application_code} ({$loanApplication->full_name}) siap dijadwalkan survei.",
            '/scheduling-simulation',
        );

        return back()->with('success', 'Berkas diajukan.');
    }

    /** Berkas hanya bisa diubah selama masih DRAFT. */
    private function locked(LoanApplication $r): ?RedirectResponse
    {
        return $r->status === 'DRAFT'
            ? null
            : back()->with('error', "Berkas berstatus {$r->status} tidak dapat diubah lagi.");
    }

    public function destroy(LoanApplication $loanApplication): RedirectResponse
    {
        if ($locked = $this->locked($loanApplication)) {
            return $locked;
        }

        $code = $loanApplication->application_code;
        $loanApplication->delete();

        ActivityLog::record("Menghapus pengajuan {$code}", self::LABEL, 'warning');

        return to_route('loan-simulation.index')->with('success', "Pengajuan {$code} dihapus.");
    }

    /** Kelengkapan tiap tahap untuk kartu konfirmasi. */
    private function checklist(LoanApplication $r): array
    {
        // Satu panggilan per request ke API Codex.
        try {
            $this->customerCache ??= CustomerDirectory::find((string) $r->nik);
        } catch (Throwable $e) {
            report($e);
            $this->customerCache = null;
        }

        return [
            'nasabah' => (bool) $this->customerCache,
            'pengajuan' => (bool) $r->product_id && (bool) $r->committee_path_id && (bool) $r->office_id
                && (bool) $r->supervisor_id && $r->requested_amount > 0 && $r->requested_tenor > 0,
            'jaminan' => ! $this->collateralRequired($r) || $r->collaterals()->exists(),
        ];
    }

    /** Agunan wajib bila parameter produk (SK Direksi) menyatakan demikian. */
    private function collateralRequired(LoanApplication $r): bool
    {
        if (! $r->product_id) {
            return false;
        }

        return (bool) ProductParameter::where('product_id', $r->product_id)->value('collateral_required');
    }

    private function references(): array
    {
        $byRoles = fn (array $roles) => User::query()
            ->whereHas('roles', fn ($q) => $q->whereIn('name', $roles))
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (User $u) => ['value' => $u->id, 'label' => $u->name])
            ->all();

        return [
            'statuses' => self::STATUSES,
            'usageTypes' => collect(self::USAGE_TYPES)->map(fn ($v) => ['value' => $v, 'label' => $v])->all(),
            'offices' => Office::orderBy('code')->get(['id', 'alias', 'name'])
                ->map(fn ($o) => ['value' => $o->id, 'label' => "{$o->alias} : {$o->name}"])->all(),
            'products' => Product::where('is_active', true)->orderBy('code')->get(['id', 'alias', 'name'])
                ->map(fn ($p) => ['value' => $p->id, 'label' => "{$p->alias} : {$p->name}"])->all(),
            'institutions' => Institution::orderBy('code')->get(['id', 'code', 'name'])
                ->map(fn ($i) => ['value' => $i->id, 'label' => "{$i->code} : {$i->name}"])->all(),
            'methods' => Method::orderBy('code')->get(['id', 'code', 'name'])
                ->map(fn ($m) => ['value' => $m->id, 'label' => "{$m->code} : {$m->name}"])->all(),
            'installments' => Installment::orderBy('code')->get(['id', 'code', 'name'])
                ->map(fn ($i) => ['value' => $i->id, 'label' => "{$i->code} : {$i->name}"])->all(),
            'supervisors' => $byRoles(['Kasi Analis']),
            'collateralTypes' => CollateralType::orderBy('code')->get(['code', 'name'])
                ->map(fn ($t) => ['value' => $t->code, 'label' => "{$t->code} : {$t->name}"])->all(),
            'bindingTypes' => BindingType::orderBy('code')->get(['code', 'name'])
                ->map(fn ($t) => ['value' => $t->code, 'label' => "{$t->code} : {$t->name}"])->all(),
            'regionOptions' => Region::query()
                ->select('code', 'regency')
                ->distinct()
                ->orderBy('code')
                ->get()
                ->map(fn (Region $r) => ['value' => $r->code, 'label' => "{$r->code} : {$r->regency}"])
                ->all(),
            'parameterMap' => ProductParameter::all()
                ->keyBy(fn (ProductParameter $p) => (string) $p->product_id)
                ->map(fn (ProductParameter $p) => [
                    'method_ids' => (array) ($p->allowed_method_ids ?? []),
                    'installment_ids' => (array) ($p->allowed_installment_ids ?? []),
                    'default_method_id' => $p->default_method_id,
                    'default_installment_id' => $p->default_installment_id,
                    'interest_rate' => $p->interest_rate,
                    'provision_rate' => $p->provision_rate,
                    'admin_rate' => $p->admin_rate,
                ]),
            'categoryMap' => CommitteePath::where('is_active', true)
                ->orderBy('condition')
                ->get(['id', 'product_id', 'condition'])
                ->groupBy(fn (CommitteePath $p) => (string) ($p->product_id ?? 'global'))
                ->map(fn ($paths) => $paths->map(fn (CommitteePath $p) => [
                    'value' => $p->id,
                    'label' => $p->condition ?: 'Normal',
                ])->values()->all()),
        ];
    }

    private function detail(LoanApplication $r): array
    {
        return [
            ...$this->row($r),
            ...$r->only([
                'institution_id', 'marketing', 'committee_path_id', 'usage_type', 'method_id',
                'installment_id', 'interest_rate', 'cif_number', 'supervisor_id',
            ]),
            'checklist' => $this->checklist($r),
            'collateral_required' => $this->collateralRequired($r),
        ];
    }

    private function row(LoanApplication $r): array
    {
        return [
            ...$r->only([
                'id', 'application_code', 'status', 'office_id', 'product_id',
                'nik', 'full_name', 'requested_amount', 'requested_tenor', 'credit_account',
            ]),
            'application_date' => $r->application_date?->format('Y-m-d'),
            'product_label' => $r->product ? "{$r->product->alias} : {$r->product->name}" : null,
            'method_label' => $r->method ? "{$r->method->code} : {$r->method->name}" : null,
            'office_label' => $r->office ? "{$r->office->alias} : {$r->office->name}" : null,
        ];
    }
}
