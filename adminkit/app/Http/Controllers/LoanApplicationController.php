<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Installment;
use App\Models\LoanApplication;
use App\Models\Method;
use App\Models\Office;
use App\Models\Product;
use App\Models\Region;
use App\Support\TableQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Pengajuan Kredit — tahap 1 dari 9. Berkas direkam apa adanya;
 * analisa (metode per produk) dan posting ke core banking menyusul.
 */
class LoanApplicationController extends Controller
{
    private const LABEL = 'Pengajuan Kredit';

    public const STATUSES = ['DIAJUKAN', 'ANALISA', 'KOMITE', 'DISETUJUI', 'DITOLAK', 'DIBATALKAN', 'REALISASI'];

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
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('LoanSimulationForm', [
            'record' => null,
            'nextCode' => LoanApplication::nextCode(),
            ...$this->references(),
        ]);
    }

    public function edit(LoanApplication $loanApplication): Response
    {
        return Inertia::render('LoanSimulationForm', [
            'record' => $this->row($loanApplication, full: true),
            'nextCode' => $loanApplication->application_code,
            ...$this->references(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->prepare($this->validated($request));
        $data['application_code'] = LoanApplication::nextCode();
        $data['created_by'] = $request->user()->id;

        $record = LoanApplication::create($data);

        ActivityLog::record("Menambah pengajuan {$record->application_code}", self::LABEL, 'success', $record);

        return to_route('loan-simulation.index')
            ->with('success', "Pengajuan {$record->application_code} ditambahkan.");
    }

    public function update(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        $before = $loanApplication->getOriginal();
        $loanApplication->update($this->prepare($this->validated($request, $loanApplication)));

        ActivityLog::record(
            "Memperbarui pengajuan {$loanApplication->application_code}",
            self::LABEL,
            'info',
            $loanApplication,
            ActivityLog::diffOf($loanApplication, $before),
        );

        return to_route('loan-simulation.index')
            ->with('success', "Pengajuan {$loanApplication->application_code} diperbarui.");
    }

    public function destroy(LoanApplication $loanApplication): RedirectResponse
    {
        $code = $loanApplication->application_code;
        $loanApplication->delete();

        ActivityLog::record("Menghapus pengajuan {$code}", self::LABEL, 'warning');

        return back()->with('success', "Pengajuan {$code} dihapus.");
    }

    /** Teks disimpan huruf besar dan angka dipastikan bertipe bilangan. */
    private function prepare(array $data): array
    {
        foreach (['full_name', 'birth_place', 'mother_name', 'address', 'occupation', 'employer_name', 'spouse_name', 'purpose', 'economic_sector', 'collateral_note'] as $key) {
            if (filled($data[$key] ?? null)) {
                $data[$key] = mb_strtoupper($data[$key]);
            }
        }

        foreach (['monthly_income', 'other_income', 'monthly_expense', 'spouse_income', 'requested_amount', 'requested_tenor'] as $key) {
            $data[$key] = (int) ($data[$key] ?? 0);
        }

        return [
            ...$data,
            'application_date' => $data['application_date'] ?? now()->toDateString(),
            'status' => $data['status'] ?? 'DIAJUKAN',
        ];
    }

    private function validated(Request $request, ?LoanApplication $current = null): array
    {
        return $request->validate([
            'application_date' => ['nullable', 'date'],
            'status' => ['nullable', Rule::in(self::STATUSES)],
            'office_id' => ['nullable', 'integer', 'exists:offices,id'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'economic_sector' => ['nullable', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:30'],

            'cif_number' => ['nullable', 'string', 'max:20'],
            'nik' => ['required', 'string', 'digits_between:8,20'],
            'full_name' => ['required', 'string', 'min:3', 'max:100'],
            'birth_place' => ['nullable', 'string', 'max:60'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:L,P'],
            'marital_status' => ['nullable', 'string', 'max:20'],
            'mother_name' => ['nullable', 'string', 'max:100'],
            'npwp' => ['nullable', 'string', 'max:25'],
            'address' => ['nullable', 'string', 'max:255'],
            'region_code' => ['nullable', 'string', 'max:8'],
            'region_label' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:25'],
            'email' => ['nullable', 'email', 'max:100'],
            'occupation' => ['nullable', 'string', 'max:60'],
            'employer_name' => ['nullable', 'string', 'max:100'],
            'monthly_income' => ['nullable', 'integer', 'min:0'],
            'other_income' => ['nullable', 'integer', 'min:0'],
            'monthly_expense' => ['nullable', 'integer', 'min:0'],
            'spouse_name' => ['nullable', 'string', 'max:100'],
            'spouse_nik' => ['nullable', 'string', 'digits_between:8,20'],
            'spouse_income' => ['nullable', 'integer', 'min:0'],

            'requested_amount' => ['nullable', 'integer', 'min:0'],
            'requested_tenor' => ['nullable', 'integer', 'min:0', 'max:600'],
            'method_id' => ['nullable', 'integer', 'exists:methods,id'],
            'installment_id' => ['nullable', 'integer', 'exists:installments,id'],
            'interest_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'provision_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'admin_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'collateral_note' => ['nullable', 'string', 'max:255'],

            'credit_account' => [
                'nullable', 'string', 'max:30',
                Rule::unique('loan_applications', 'credit_account')->ignore($current?->id),
            ],
        ], [
            'nik.digits_between' => 'Kolom nomor KTP hanya boleh angka (8–20 digit).',
        ], [
            'nik' => 'nomor ktp',
            'full_name' => 'nama lengkap',
            'requested_amount' => 'plafon diajukan',
            'requested_tenor' => 'jangka waktu',
            'product_id' => 'produk',
            'office_id' => 'kantor',
        ]);
    }

    private function references(): array
    {
        return [
            'statuses' => self::STATUSES,
            'offices' => Office::orderBy('code')->get(['id', 'code', 'name'])
                ->map(fn ($o) => ['value' => $o->id, 'label' => "{$o->code} : {$o->name}"])->all(),
            'products' => Product::orderBy('code')->get(['id', 'code', 'name'])
                ->map(fn ($p) => ['value' => $p->id, 'label' => "{$p->code} : {$p->name}"])->all(),
            'methods' => Method::orderBy('code')->get(['id', 'code', 'name'])
                ->map(fn ($m) => ['value' => $m->id, 'label' => "{$m->code} : {$m->name}"])->all(),
            'installments' => Installment::orderBy('code')->get(['id', 'code', 'name'])
                ->map(fn ($i) => ['value' => $i->id, 'label' => "{$i->code} : {$i->name}"])->all(),
            'regionOptions' => Region::query()
                ->select('code', 'regency')
                ->distinct()
                ->orderBy('code')
                ->get()
                ->map(fn (Region $r) => ['value' => $r->code, 'label' => "{$r->code} : {$r->regency}"])
                ->all(),
        ];
    }

    private function row(LoanApplication $r, bool $full = false): array
    {
        $base = [
            ...$r->only([
                'id', 'application_code', 'status', 'office_id', 'product_id',
                'nik', 'full_name', 'requested_amount', 'requested_tenor', 'credit_account',
            ]),
            'application_date' => $r->application_date?->format('Y-m-d'),
            'product_label' => $r->product ? "{$r->product->code} : {$r->product->name}" : null,
            'office_label' => $r->office ? "{$r->office->code} : {$r->office->name}" : null,
        ];

        if (! $full) {
            return $base;
        }

        return [
            ...$base,
            ...$r->only([
                'purpose', 'economic_sector', 'source', 'cif_number', 'birth_place', 'gender',
                'marital_status', 'mother_name', 'npwp', 'address', 'region_code', 'region_label',
                'phone', 'email', 'occupation', 'employer_name', 'monthly_income', 'other_income',
                'monthly_expense', 'spouse_name', 'spouse_nik', 'spouse_income', 'method_id',
                'installment_id', 'interest_rate', 'provision_rate', 'admin_rate', 'collateral_note',
            ]),
            'birth_date' => $r->birth_date?->format('Y-m-d'),
        ];
    }
}
