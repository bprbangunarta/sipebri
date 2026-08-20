<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\CollateralSimulation;
use App\Models\Installment;
use App\Models\Institution;
use App\Models\LoanApplication;
use App\Models\Method;
use App\Models\Office;
use App\Models\Product;
use App\Models\User;
use App\Support\CustomerDirectory;
use App\Support\TableQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Pengajuan Kredit — tahap 1 dari 9.
 * Data pemohon TIDAK disimpan: identitas diambil dari API sistem nasabah (CustomerDirectory)
 * memakai nomor KTP. Berkas hanya menyimpan nik, nama, dan nomor CIF.
 */
class LoanApplicationController extends Controller
{
    private const LABEL = 'Pengajuan Kredit';

    public const STATUSES = ['DIAJUKAN', 'ANALISA', 'KOMITE', 'DISETUJUI', 'DITOLAK', 'DIBATALKAN', 'REALISASI'];

    public const USAGE_TYPES = ['KONSUMTIF', 'PRODUKTIF', 'INVESTASI'];

    private ?array $customerCache = null;

    public function index(Request $request): Response
    {
        $search = TableQuery::search($request);
        $sort = TableQuery::sort($request, ['application_code', 'application_date', 'full_name', 'requested_amount', 'status'], 'application_code');
        $dir = TableQuery::direction($request);
        $status = (string) $request->input('status', '');

        $records = LoanApplication::query()
            ->with(['product:id,code,name', 'office:id,code,name'])
            ->when($search !== '', fn ($q) => $q->where(function ($w) use ($search) {
                foreach (['application_code', 'full_name', 'nik', 'credit_account'] as $col) {
                    $w->orWhere($col, 'like', "%{$search}%");
                }
            }))
            ->when(in_array($status, self::STATUSES, true), fn ($q) => $q->where('status', $status))
            ->orderBy($sort, $dir)
            ->paginate(TableQuery::perPage($request))
            ->withQueryString();

        return Inertia::render('LoanSimulation', [
            'records' => [
                'data' => collect($records->items())->map(fn (LoanApplication $r) => $this->row($r))->all(),
                'meta' => TableQuery::meta($records),
            ],
            'filters' => ['search' => $search, 'sort' => $sort, 'dir' => $dir, 'status' => $status],
            'statuses' => self::STATUSES,
            'sampleNiks' => CustomerDirectory::sampleNiks(),
        ]);
    }

    /** Identitas nasabah dari sistem lain (MOCK sampai endpoint API siap). */
    public function lookup(Request $request): JsonResponse
    {
        $nik = (string) $request->query('nik', '');
        $customer = CustomerDirectory::find($nik);

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
        $loanApplication->load(['collaterals', 'product:id,code,name', 'office:id,code,name']);

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

        $customer = CustomerDirectory::find($data['nik']);

        if (! $customer) {
            throw ValidationException::withMessages([
                'nik' => 'Nomor KTP belum terdaftar di sistem data nasabah. Pengajuan tidak dapat dilanjutkan.',
            ]);
        }

        $record = LoanApplication::create([
            'application_code' => LoanApplication::nextCode(),
            'application_date' => now()->toDateString(),
            'status' => 'DIAJUKAN',
            'nik' => $data['nik'],
            'full_name' => $customer['full_name'],
            'cif_number' => $customer['cif_number'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        ActivityLog::record("Menambah pengajuan {$record->application_code}", self::LABEL, 'success', $record);

        return to_route('loan-simulation.show', $record)
            ->with('success', "Pengajuan {$record->application_code} dibuka. Lengkapi tahapannya.");
    }

    /** Tahap "Data Pengajuan". */
    public function update(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        $before = $loanApplication->getOriginal();

        $data = $request->validate([
            'application_date' => ['required', 'date'],
            'office_id' => ['nullable', 'integer', 'exists:offices,id'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'institution_id' => ['nullable', 'integer', 'exists:institutions,id'],
            'requested_amount' => ['required', 'integer', 'min:1'],
            'requested_tenor' => ['required', 'integer', 'min:1', 'max:600'],
            'tenor_principal' => ['nullable', 'integer', 'min:1', 'max:600', 'lte:requested_tenor'],
            'tenor_interest' => ['nullable', 'integer', 'min:1', 'max:600', 'lte:requested_tenor'],
            'usage_type' => ['nullable', Rule::in(self::USAGE_TYPES)],
            'method_id' => ['nullable', 'integer', 'exists:methods,id'],
            'installment_id' => ['nullable', 'integer', 'exists:installments,id'],
            'interest_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'provision_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'admin_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:255'],
        ], [], [
            'product_id' => 'produk',
            'requested_amount' => 'plafon',
            'requested_tenor' => 'jangka waktu',
            'tenor_principal' => 'jk pokok',
            'tenor_interest' => 'jw bunga',
        ]);

        foreach (['purpose', 'note'] as $key) {
            if (filled($data[$key] ?? null)) {
                $data[$key] = mb_strtoupper($data[$key]);
            }
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

    /** Tahap "Data Surveyor". */
    public function updateSurvey(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        $data = $request->validate([
            'office_id' => ['required', 'integer', 'exists:offices,id'],
            'supervisor_id' => ['nullable', 'integer', 'exists:users,id'],
            'surveyor_id' => ['nullable', 'integer', 'exists:users,id'],
        ], [], ['office_id' => 'wilayah/kantor']);

        $loanApplication->update($data);

        return back()->with('success', 'Penugasan surveyor disimpan.');
    }

    public function attachCollateral(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        $data = $request->validate([
            'collateral_simulation_id' => ['required', 'integer', 'exists:collateral_simulations,id'],
        ], [], ['collateral_simulation_id' => 'agunan']);

        $loanApplication->collaterals()->syncWithoutDetaching([$data['collateral_simulation_id']]);

        return back()->with('success', 'Agunan dilekatkan ke berkas.');
    }

    public function detachCollateral(LoanApplication $loanApplication, CollateralSimulation $collateral): RedirectResponse
    {
        $loanApplication->collaterals()->detach($collateral->id);

        return back()->with('success', 'Agunan dilepas dari berkas.');
    }

    /** Tahap "Konfirmasi Data" — berkas berpindah ke tahap analisa. */
    public function confirm(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        if ($loanApplication->confirmed_at) {
            return back()->with('error', 'Berkas sudah dikonfirmasi.');
        }

        $checks = $this->checklist($loanApplication);

        if (in_array(false, $checks, true)) {
            return back()->with('error', 'Lengkapi seluruh tahapan sebelum konfirmasi.');
        }

        $loanApplication->update([
            'status' => 'ANALISA',
            'confirmed_at' => now(),
            'confirmed_by' => $request->user()->id,
        ]);

        ActivityLog::record("Konfirmasi pengajuan {$loanApplication->application_code}", self::LABEL, 'success', $loanApplication);

        return back()->with('success', 'Berkas dikonfirmasi dan masuk tahap analisa.');
    }

    public function destroy(LoanApplication $loanApplication): RedirectResponse
    {
        $code = $loanApplication->application_code;
        $loanApplication->delete();

        ActivityLog::record("Menghapus pengajuan {$code}", self::LABEL, 'warning');

        return to_route('loan-simulation.index')->with('success', "Pengajuan {$code} dihapus.");
    }

    /** Kelengkapan tiap tahap untuk kartu konfirmasi. */
    private function checklist(LoanApplication $r): array
    {
        // Satu panggilan per request; nanti diganti HTTP ke sistem nasabah.
        $this->customerCache ??= CustomerDirectory::find((string) $r->nik);

        return [
            'nasabah' => (bool) $this->customerCache,
            'pengajuan' => (bool) $r->product_id && $r->requested_amount > 0 && $r->requested_tenor > 0,
            'jaminan' => $r->collaterals()->exists(),
            'surveyor' => (bool) $r->office_id && (bool) $r->surveyor_id,
        ];
    }

    private function references(): array
    {
        $byRoles = fn (array $roles) => User::query()
            ->whereHas('roles', fn ($q) => $q->whereIn('name', $roles))
            ->orderBy('name')
            ->get(['id', 'name', 'role'])
            ->map(fn (User $u) => ['value' => $u->id, 'label' => "{$u->name} — {$u->role}"])
            ->all();

        return [
            'statuses' => self::STATUSES,
            'usageTypes' => collect(self::USAGE_TYPES)->map(fn ($v) => ['value' => $v, 'label' => $v])->all(),
            'offices' => Office::orderBy('code')->get(['id', 'code', 'name'])
                ->map(fn ($o) => ['value' => $o->id, 'label' => "{$o->code} : {$o->name}"])->all(),
            'products' => Product::orderBy('code')->get(['id', 'code', 'name'])
                ->map(fn ($p) => ['value' => $p->id, 'label' => "{$p->code} : {$p->name}"])->all(),
            'institutions' => Institution::orderBy('name')->get(['id', 'name'])
                ->map(fn ($i) => ['value' => $i->id, 'label' => $i->name])->all(),
            'methods' => Method::orderBy('code')->get(['id', 'code', 'name'])
                ->map(fn ($m) => ['value' => $m->id, 'label' => "{$m->code} : {$m->name}"])->all(),
            'installments' => Installment::orderBy('code')->get(['id', 'code', 'name'])
                ->map(fn ($i) => ['value' => $i->id, 'label' => "{$i->code} : {$i->name}"])->all(),
            'supervisors' => $byRoles(['Kasi Analis', 'Kabag Analis']),
            'surveyors' => $byRoles(['Staff Analis', 'AO Kredit']),
        ];
    }

    private function detail(LoanApplication $r): array
    {
        return [
            ...$this->row($r),
            ...$r->only([
                'institution_id', 'tenor_principal', 'tenor_interest', 'usage_type', 'method_id',
                'installment_id', 'interest_rate', 'provision_rate', 'admin_rate', 'purpose',
                'note', 'collateral_note', 'cif_number', 'supervisor_id', 'surveyor_id',
            ]),
            'confirmed_at' => $r->confirmed_at?->translatedFormat('d M Y H:i'),
            'checklist' => $this->checklist($r),
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
            'product_label' => $r->product ? "{$r->product->code} : {$r->product->name}" : null,
            'office_label' => $r->office ? "{$r->office->code} : {$r->office->name}" : null,
        ];
    }
}
