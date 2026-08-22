<?php

namespace App\Http\Controllers;

use App\Enums\RoleName;
use App\Models\ActivityLog;
use App\Models\AnalysisMemorandum;
use App\Models\AnalysisSheet;
use App\Models\CommitteeTier;
use App\Models\LoanApplication;
use App\Models\LoanApproval;
use App\Models\Method;
use App\Models\ProductParameter;
use App\Support\Notify;
use App\Support\TableQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Persetujuan Komite Kredit — tahap terakhir alur kredit.
 * Berkas berstatus KOMITE diputus berjenjang mengikuti jalur komite produk:
 * jenjang yang belum berwenang hanya bisa TERUSKAN (naik komite), jenjang
 * berwenang memutus DISETUJUI / DITOLAK / DIBATALKAN.
 */
class ApprovalController extends Controller
{
    private const LABEL = 'Persetujuan Komite';

    /** Ambang RC bawaan bila produk belum punya parameter. */
    private const RC_FALLBACK = 70.0;

    public function index(Request $request): Response
    {
        $search = TableQuery::search($request);
        $sort = TableQuery::sort(
            $request,
            ['application_code', 'application_date', 'full_name', 'requested_amount'],
            'application_code',
        );
        $dir = TableQuery::direction($request);

        $records = LoanApplication::query()
            ->with([
                'product:id,alias,name', 'office:id,alias,name', 'supervisor:id,name',
                'committeePath:id,product_id,condition,mechanism',
                'committeePath.product:id,alias', 'committeePath.tiers', 'approvals',
            ])
            ->where('status', 'KOMITE')
            ->when($search !== '', fn ($q) => $q->where(function ($w) use ($search) {
                foreach (['application_code', 'full_name', 'nik', 'requested_amount'] as $col) {
                    $w->orWhere($col, 'like', "%{$search}%");
                }

                $w->orWhereHas('product', fn ($p) => $p
                    ->where('alias', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%"));
            }))
            ->orderBy($sort, $dir)
            ->paginate(TableQuery::perPage($request))
            ->withQueryString();

        return Inertia::render('ApprovalSimulation', [
            'records' => [
                'data' => collect($records->items())->map(fn (LoanApplication $r) => $this->row($r))->all(),
                'meta' => TableQuery::meta($records),
            ],
            'filters' => ['search' => $search, 'sort' => $sort, 'dir' => $dir],
        ]);
    }

    /** Lembar keputusan komite satu berkas. */
    public function show(Request $request, LoanApplication $loanApplication): Response
    {
        abort_unless($loanApplication->status === 'KOMITE', 404, 'Berkas tidak menunggu keputusan komite.');

        $loanApplication->load(['product:id,alias,name', 'office:id,alias,name', 'supervisor:id,name',
            'committeePath:id,product_id,condition,mechanism', 'committeePath.product:id,alias',
            'committeePath.tiers', 'approvals.user:id,name', 'approvals.method:id,name', 'method:id,name']);

        $basis = $this->basis($loanApplication);
        $tier = $this->pendingTier($loanApplication);
        $allowed = $tier ? $this->allowedDecisions($tier, $basis['amount']) : [];
        $mayDecide = $tier && $this->mayDecide($request, $tier);

        return Inertia::render('ApprovalDetail', [
            'record' => $this->row($loanApplication),
            'tiers' => $this->tierRows($loanApplication),
            'analyst' => $this->analystNote($loanApplication, $basis),
            'basis' => $basis,
            'flow' => [
                'pending_level' => $tier?->sort,
                'pending_position' => $tier ? "{$tier->label} · {$tier->role}" : null,
                'my_turn' => $mayDecide,
                'allowed' => $mayDecide ? $allowed : [],
                'blocked_reason' => $this->blockedReason($tier, $mayDecide, $allowed),
            ],
            'methodOptions' => $this->methodOptions($loanApplication),
        ]);
    }

    /** Simpan keputusan satu jenjang. */
    public function decide(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        abort_unless($loanApplication->status === 'KOMITE', 404, 'Berkas tidak menunggu keputusan komite.');

        $tier = $this->pendingTier($loanApplication);
        abort_if($tier === null, 422, 'Jalur komite berkas ini belum punya jenjang pemutus.');
        abort_unless($this->mayDecide($request, $tier), 403, 'Anda bukan pemutus pada jenjang ini.');

        $basis = $this->basis($loanApplication);
        $percent = ['required', 'numeric', 'min:0', 'max:100'];

        $data = $request->validate([
            'decision' => ['required', Rule::in($this->allowedDecisions($tier, $basis['amount']))],
            'method_id' => ['required', 'integer', 'exists:methods,id'],
            'amount' => ['required', 'integer', 'min:1', 'max:999999999999'],
            'tenor' => ['required', 'integer', 'min:1', 'max:300'],
            'interest_rate' => $percent,
            'provision_rate' => $percent,
            'admin_rate' => $percent,
            'note' => ['nullable', 'string', 'max:255'],
        ], [], [
            'decision' => 'keputusan komite', 'method_id' => 'metode RPS', 'amount' => 'usulan plafon',
            'tenor' => 'jangka waktu', 'interest_rate' => 'suku bunga',
            'provision_rate' => 'biaya provisi', 'admin_rate' => 'biaya admin', 'note' => 'catatan komite',
        ]);

        $methodName = Method::whereKey($data['method_id'])->value('name') ?? '';
        $maxAmount = $this->maxPlafon(
            $basis['capacity'],
            $basis['rc_threshold'],
            (float) $data['interest_rate'],
            (int) $data['tenor'],
            $methodName,
        );

        LoanApproval::create([
            'loan_application_id' => $loanApplication->id,
            'committee_tier_id' => $tier->id,
            'level' => $tier->sort,
            'role' => $tier->role,
            'user_id' => $request->user()->id,
            'method_id' => $data['method_id'],
            'decision' => $data['decision'],
            'max_amount' => $maxAmount,
            'amount' => $data['amount'],
            'tenor' => $data['tenor'],
            'interest_rate' => $data['interest_rate'],
            'provision_rate' => $data['provision_rate'],
            'admin_rate' => $data['admin_rate'],
            'rc_ratio' => $this->rcRatio((int) $data['amount'], $maxAmount),
            'note' => $data['note'] ?: null,
            'decided_at' => now(),
        ]);

        $data['decision'] === 'TERUSKAN'
            ? $this->escalate($loanApplication, $tier, $data)
            : $this->finalize($request, $loanApplication, $tier, $data, $maxAmount);

        return redirect()->route('approval-simulation.index')->with(
            'success',
            $data['decision'] === 'TERUSKAN'
                ? "Berkas {$loanApplication->application_code} diteruskan ke jenjang berikutnya."
                : "Berkas {$loanApplication->application_code} {$data['decision']}.",
        );
    }

    /** Naik komite: berkas tetap berstatus KOMITE, jenjang berikutnya diberi tahu. */
    private function escalate(LoanApplication $r, CommitteeTier $tier, array $data): void
    {
        $next = $r->committeePath?->tiers->firstWhere('sort', $tier->sort + 1);

        ActivityLog::record(
            "Meneruskan berkas {$r->application_code} ke jenjang ".($next?->label ?? 'berikutnya'),
            self::LABEL,
            'info',
            $r,
        );

        Notify::toPermission(
            'approval-simulation.manage',
            'Berkas menunggu keputusan Anda',
            self::LABEL,
            "Berkas {$r->application_code} - {$r->full_name} diteruskan ke jenjang ".($next?->label ?? 'berikutnya').'.',
            "/approval-simulation/{$r->id}",
        );
    }

    /** Keputusan akhir: status berkas berubah dan pengusul diberi tahu. */
    private function finalize(Request $request, LoanApplication $r, CommitteeTier $tier, array $data, int $maxAmount): void
    {
        $approved = $data['decision'] === 'DISETUJUI';

        $r->update([
            'status' => $data['decision'],
            'decision' => $data['decision'],
            'decided_at' => now(),
            'decided_by' => $request->user()->id,
            'decision_note' => $data['note'] ?: null,
            'approved_amount' => $approved ? $data['amount'] : 0,
            'approved_tenor' => $approved ? $data['tenor'] : 0,
            'approved_rate' => $approved ? $data['interest_rate'] : 0,
            'rc_ratio' => $this->rcRatio((int) $data['amount'], $maxAmount),
        ]);

        ActivityLog::record(
            "Berkas {$r->application_code} {$data['decision']} oleh {$tier->label} ({$tier->role})",
            self::LABEL,
            $approved ? 'success' : 'warning',
            $r,
        );

        if ($r->supervisor) {
            Notify::toUser(
                $r->supervisor,
                "Keputusan komite: {$data['decision']}",
                self::LABEL,
                "Berkas {$r->application_code} - {$r->full_name} diputus {$data['decision']} oleh {$tier->label}.",
                '/loan-simulation',
                $approved ? 'success' : 'warning',
            );
        }

        Notify::toPermission(
            'analysis-simulation.manage',
            "Keputusan komite: {$data['decision']}",
            self::LABEL,
            "Berkas {$r->application_code} - {$r->full_name} sudah diputus komite.",
            '/loan-simulation',
            $approved ? 'success' : 'warning',
        );
    }

    /** Jenjang yang sedang menunggu keputusan (setelah semua TERUSKAN sebelumnya). */
    private function pendingTier(LoanApplication $r): ?CommitteeTier
    {
        $tiers = $r->committeePath?->tiers ?? collect();
        $level = (int) $r->approvals->max('level') + 1;

        return $tiers->firstWhere('sort', $level) ?? $tiers->last();
    }

    /** Keputusan yang boleh dipilih jenjang ini atas nominal usulan. */
    private function allowedDecisions(CommitteeTier $tier, int $amount): array
    {
        $ceiling = $tier->max_amount === null ? null : (int) $tier->max_amount;
        $out = [];

        if ($tier->can_escalate) {
            $out[] = 'TERUSKAN';
        }

        if ($tier->can_approve && ($ceiling === null || $amount <= $ceiling)) {
            $out[] = 'DISETUJUI';
        }

        if ($tier->can_reject) {
            $out[] = 'DITOLAK';
        }

        if ($tier->can_cancel) {
            $out[] = 'DIBATALKAN';
        }

        return $out;
    }

    private function mayDecide(Request $request, CommitteeTier $tier): bool
    {
        $user = $request->user();

        return $user->hasRole(RoleName::SuperAdmin->value)
            || ($tier->role !== null && $user->hasRole($tier->role));
    }

    private function blockedReason(?CommitteeTier $tier, bool $mayDecide, array $allowed): ?string
    {
        if ($tier === null) {
            return 'Jalur komite berkas ini belum punya jenjang pemutus.';
        }

        if (! $mayDecide) {
            return "Berkas menunggu keputusan {$tier->label} ({$tier->role}).";
        }

        return $allowed === [] ? 'Jenjang ini tidak memiliki kewenangan keputusan.' : null;
    }

    /** Dasar hitung: kemampuan angsuran, ambang RC produk, max plafon dan RC usulan. */
    private function basis(LoanApplication $r): array
    {
        $memorandum = AnalysisMemorandum::firstOrNew(['loan_application_id' => $r->id]);
        $sheet = AnalysisSheet::firstOrNew(['loan_application_id' => $r->id]);
        $capacity = max(0, (int) $sheet->metrics()['monthly_balance']);
        $threshold = (float) (ProductParameter::where('product_id', $r->product_id)->value('rc_threshold')
            ?: self::RC_FALLBACK);

        $amount = (int) ($memorandum->usulan_plafond ?: $r->requested_amount);
        $tenor = (int) ($memorandum->jangka_waktu ?: $r->requested_tenor);
        $rate = (float) ($memorandum->s_bunga ?: $r->interest_rate);
        $methodId = (int) $r->method_id;
        $maxAmount = $this->maxPlafon($capacity, $threshold, $rate, $tenor, (string) $r->method?->name);

        return [
            'capacity' => $capacity,
            'rc_threshold' => $threshold,
            'max_amount' => $maxAmount,
            'rc_ratio' => $this->rcRatio($amount, $maxAmount),
            'amount' => $amount,
            'tenor' => $tenor,
            'interest_rate' => $rate,
            'provision_rate' => (float) ($memorandum->b_provisi ?: $r->provision_rate),
            'admin_rate' => (float) ($memorandum->b_admin ?: $r->admin_rate),
            'method_id' => $methodId ?: '',
            'method_label' => $r->method?->name,
            'taksasi' => (int) $r->collaterals()->sum('appraisal_value'),
        ];
    }

    /**
     * Plafon maksimum dari kemampuan angsuran: (keuangan per bulan × ambang RC)
     * dikapitalisasi memakai metode bunga & jangka waktu usulan.
     */
    private function maxPlafon(int $capacity, float $threshold, float $rate, int $tenor, string $method): int
    {
        $installment = $capacity * ($threshold / 100);

        if ($installment <= 0 || $tenor <= 0) {
            return 0;
        }

        $i = $rate / 100 / 12;

        if ($i <= 0) {
            return (int) round($installment * $tenor);
        }

        $effective = str_contains(strtoupper($method), 'ANUITAS') || str_contains(strtoupper($method), 'EFEKTIF');

        return (int) round($effective
            ? $installment * (1 - (1 + $i) ** -$tenor) / $i
            : $installment / (1 / $tenor + $i));
    }

    /** RC sistem lama: porsi usulan plafon terhadap max plafon (%). */
    private function rcRatio(int $amount, int $maxAmount): float
    {
        return $maxAmount > 0 ? round($amount / $maxAmount * 100, 2) : 0.0;
    }

    /** Baris "Staff Analis" pada catatan komite. */
    private function analystNote(LoanApplication $r, array $basis): array
    {
        $memorandum = AnalysisMemorandum::firstOrNew(['loan_application_id' => $r->id]);

        return [
            'name' => $r->analysis_submitted_by,
            'amount' => $basis['amount'],
            'tenor' => $basis['tenor'],
            'method_label' => $basis['method_label'],
            'provision_rate' => $basis['provision_rate'],
            'admin_rate' => $basis['admin_rate'],
            'interest_rate' => $basis['interest_rate'],
            'rc_ratio' => $basis['rc_ratio'],
            'note' => $memorandum->syarat_tambahan ?: $r->analysis_note,
            'decided_at' => $r->analysis_submitted_at?->translatedFormat('d M Y H:i'),
        ];
    }

    /** Jenjang + keputusan yang sudah tercatat pada tiap jenjang. */
    private function tierRows(LoanApplication $r): array
    {
        $approvals = $r->approvals->keyBy('level');

        return ($r->committeePath?->tiers ?? collect())
            ->map(function (CommitteeTier $t) use ($approvals) {
                $a = $approvals->get($t->sort);

                return [
                    'level' => $t->sort,
                    'position' => $t->label,
                    'role' => $t->role,
                    'min_amount' => (int) $t->min_amount,
                    'max_amount' => $t->max_amount === null ? null : (int) $t->max_amount,
                    'can_approve' => (bool) $t->can_approve,
                    'can_reject' => (bool) $t->can_reject,
                    'can_escalate' => (bool) $t->can_escalate,
                    'decision' => $a?->decision,
                    'note' => $a?->note,
                    'amount' => (int) ($a->amount ?? 0),
                    'tenor' => (int) ($a->tenor ?? 0),
                    'rc_ratio' => (float) ($a->rc_ratio ?? 0),
                    'method_label' => $a?->method?->name,
                    'provision_rate' => (float) ($a->provision_rate ?? 0),
                    'admin_rate' => (float) ($a->admin_rate ?? 0),
                    'decided_by' => $a?->user?->name,
                    'decided_at' => $a?->decided_at?->translatedFormat('d M Y H:i'),
                ];
            })
            ->values()
            ->all();
    }

    /** Metode RPS yang diizinkan produk berkas (bila tidak diatur: semua metode). */
    private function methodOptions(LoanApplication $r): array
    {
        $allowed = ProductParameter::where('product_id', $r->product_id)->value('allowed_method_ids');
        $allowed = is_string($allowed) ? json_decode($allowed, true) : $allowed;

        return Method::query()
            ->when(is_array($allowed) && $allowed !== [], fn ($q) => $q->whereIn('id', $allowed))
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Method $m) => ['value' => $m->id, 'label' => $m->name])
            ->all();
    }

    private function row(LoanApplication $r): array
    {
        $memorandum = AnalysisMemorandum::where('loan_application_id', $r->id)->first();
        $tier = $this->pendingTier($r);

        return [
            'id' => $r->id,
            'application_code' => $r->application_code,
            'application_date' => $r->application_date?->translatedFormat('d M Y'),
            'full_name' => $r->full_name,
            'nik' => $r->nik,
            'status' => $r->status,
            'product_label' => $r->product ? "{$r->product->alias} : {$r->product->name}" : null,
            'office_label' => $r->office ? "{$r->office->alias} : {$r->office->name}" : null,
            'committee_path' => $r->committeePath ? $r->committeePath->title() : null,
            'pending_position' => $tier ? "{$tier->label} · {$tier->role}" : null,
            'supervisor_name' => $r->supervisor?->name,
            'requested_amount' => (int) $r->requested_amount,
            'requested_tenor' => (int) $r->requested_tenor,
            'interest_rate' => $r->interest_rate,
            'usage_type' => $r->usage_type,
            'proposed_amount' => (int) ($memorandum->usulan_plafond ?? 0),
            'proposed_tenor' => (int) ($memorandum->jangka_waktu ?? 0),
            'submitted_at' => $r->analysis_submitted_at?->translatedFormat('d M Y H:i'),
            'submitted_by' => $r->analysis_submitted_by,
        ];
    }
}
