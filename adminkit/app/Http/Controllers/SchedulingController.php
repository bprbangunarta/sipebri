<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\LoanApplication;
use App\Models\LoanSchedule;
use App\Models\LoanSurvey;
use App\Models\User;
use App\Support\Notify;
use App\Support\TableQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Penjadwalan Survei — tahap 2 dari alur kredit.
 * Kasi Analis menetapkan tanggal survei + staff analis untuk berkas berstatus
 * DIAJUKAN. Semua penjadwalan, penjadwalan ulang, dan pembatalan dicatat di
 * `loan_schedules` dan tidak pernah dihapus.
 */
class SchedulingController extends Controller
{
    private const LABEL = 'Penjadwalan Survei';

    /**
     * Berkas yang tampil di penjadwalan. `SURVEY` ikut tampil karena hasil survei
     * bisa dinilai kurang sehingga perlu SURVEI ULANG oleh pejabat yang lebih tinggi.
     */
    private const OPEN_STATUSES = ['DIAJUKAN', 'PENJADWALAN', 'SURVEY'];

    /**
     * Tangga surveyor: survei pertama oleh Staff Analis, lalu naik setiap kali
     * survei dinilai kurang. Tidak ada perhitungan — hanya opini kelayakan.
     */
    private const LADDER = ['Staff Analis', 'Kasi Analis', 'Kabag Analis', 'Direktur Bisnis', 'Direktur Utama'];

    /** Produk khusus: 3 peranan kantor boleh survei & analisa, dan survei bisa dilewati. */
    private const WALK_IN_PRODUCT = 'KTA';

    private const WALK_IN_ROLES = ['Kepala Kantor Kas', 'Customer Service', 'Teller'];

    public function index(Request $request): Response
    {
        $search = TableQuery::search($request);
        $sort = TableQuery::sort($request, ['application_code', 'application_date', 'full_name', 'survey_date', 'status'], 'application_date');
        $dir = TableQuery::direction($request);
        $status = (string) $request->input('status', '');
        $scope = $request->input('scope') === 'semua' ? 'semua' : 'saya';

        $records = LoanApplication::query()
            ->with(['product:id,alias,name', 'office:id,alias,name', 'supervisor:id,name', 'surveyor:id,name'])
            ->whereIn('status', self::OPEN_STATUSES)
            ->when($scope === 'saya', fn ($q) => $q->where('supervisor_id', $request->user()->id))
            ->when(in_array($status, self::OPEN_STATUSES, true), fn ($q) => $q->where('status', $status))
            ->when($search !== '', fn ($q) => $q->where(function ($w) use ($search) {
                foreach (['application_code', 'full_name', 'nik', 'status', 'survey_date', 'requested_amount'] as $col) {
                    $w->orWhere($col, 'like', "%{$search}%");
                }

                $w->orWhereHas('product', fn ($p) => $p
                    ->where('alias', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%"));

                $w->orWhereHas('surveyor', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            }))
            ->orderBy($sort, $dir)
            ->paginate(TableQuery::perPage($request))
            ->withQueryString();

        return Inertia::render('Scheduling', [
            'records' => [
                'data' => collect($records->items())->map(fn (LoanApplication $r) => $this->row($r))->all(),
                'meta' => TableQuery::meta($records),
            ],
            'filters' => ['search' => $search, 'sort' => $sort, 'dir' => $dir, 'status' => $status, 'scope' => $scope],
            'statuses' => self::OPEN_STATUSES,
            'maxSchedules' => LoanSchedule::MAX_SCHEDULES,
        ]);
    }

    /** Menetapkan atau mengulang jadwal survei. */
    public function store(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        if (! in_array($loanApplication->status, self::OPEN_STATUSES, true)) {
            return back()->with('error', 'Berkas ini tidak berada pada tahap penjadwalan.');
        }

        $done = $this->scheduleCount($loanApplication);

        $allowed = collect($this->surveyors($loanApplication))->pluck('value')->all();

        $data = $request->validate([
            'survey_date' => ['required', 'date', 'after_or_equal:today'],
            'surveyor_id' => ['required', 'integer', Rule::in($allowed)],
            'note' => ['nullable', 'string', 'max:255'],
        ], [
            'surveyor_id.in' => 'Nama surveyor tidak sesuai kewenangan tahap ini.',
        ], ['survey_date' => 'tanggal survei', 'surveyor_id' => 'nama surveyor', 'note' => 'catatan']);

        $surveyor = User::findOrFail($data['surveyor_id']);

        $walkIn = $this->isWalkIn($loanApplication);
        $resurvey = $loanApplication->status === 'SURVEY';

        LoanSchedule::create([
            'loan_application_id' => $loanApplication->id,
            'sequence' => $done + 1,
            'action' => match (true) {
                $resurvey => LoanSchedule::ACTION_RESURVEY,
                $done === 0 => LoanSchedule::ACTION_SCHEDULE,
                default => LoanSchedule::ACTION_RESCHEDULE,
            },
            'survey_date' => $data['survey_date'],
            'surveyor_id' => $surveyor->id,
            'surveyor_name' => $surveyor->name,
            'note' => $data['note'] ?? null,
            'created_by' => $request->user()->name,
        ]);

        // Produk KTA: nasabah datang sendiri ke kantor, survei lapangan dilewati
        // sehingga berkas langsung terbuka untuk analisa.
        $loanApplication->update([
            'status' => $walkIn ? 'SURVEY' : 'PENJADWALAN',
            'surveyor_id' => $surveyor->id,
            'survey_date' => $data['survey_date'],
        ]);

        $label = $resurvey ? 'Menjadwalkan survei ulang' : ($done === 0 ? 'Menjadwalkan' : 'Menjadwalkan ulang');
        ActivityLog::record(
            "{$label} survei berkas {$loanApplication->application_code}",
            self::LABEL,
            'success',
            $loanApplication,
        );

        Notify::toUser(
            $surveyor,
            $walkIn ? 'Berkas KTA siap dianalisa' : 'Penugasan survei',
            self::LABEL,
            "Berkas {$loanApplication->application_code} ({$loanApplication->full_name}) "
                .($walkIn ? 'tanpa survei lapangan, langsung analisa.' : 'disurvei '
                    .$loanApplication->survey_date->translatedFormat('d M Y').'.'),
            $walkIn ? '/analysis-simulation' : '/survey-simulation',
        );

        return back()->with('success', $walkIn
            ? 'Jadwal disimpan. Produk KTA tanpa survei lapangan — berkas langsung siap dianalisa.'
            : 'Jadwal survei disimpan.');
    }

    /**
     * Staff Analis membatalkan jadwal dan meminta penjadwalan ulang.
     * Bila batas penjadwalan sudah habis, berkas dikembalikan ke DRAFT.
     */
    public function cancel(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        if ($loanApplication->status !== 'PENJADWALAN') {
            return back()->with('error', 'Berkas ini tidak memiliki jadwal survei aktif.');
        }

        $data = $request->validate(
            ['reason' => ['required', 'string', 'max:255']],
            [],
            ['reason' => 'alasan pembatalan'],
        );

        $done = $this->scheduleCount($loanApplication);

        LoanSchedule::create([
            'loan_application_id' => $loanApplication->id,
            'sequence' => $done,
            'action' => LoanSchedule::ACTION_CANCEL,
            'survey_date' => $loanApplication->survey_date,
            'surveyor_id' => $loanApplication->surveyor_id,
            'surveyor_name' => $loanApplication->surveyor?->name,
            'reason' => $data['reason'],
            'created_by' => $request->user()->name,
        ]);

        $exceeded = $done >= LoanSchedule::MAX_SCHEDULES;

        $loanApplication->update([
            'status' => 'DIAJUKAN',
            'surveyor_id' => null,
            'survey_date' => null,
        ]);

        ActivityLog::record(
            "Membatalkan jadwal survei berkas {$loanApplication->application_code}",
            self::LABEL,
            'warning',
            $loanApplication,
        );

        Notify::toPermission(
            'scheduling-simulation.manage',
            $exceeded ? 'Penjadwalan ulang melebihi batas' : 'Permintaan penjadwalan ulang',
            self::LABEL,
            "Berkas {$loanApplication->application_code}: {$data['reason']}"
                .($exceeded ? ' (sudah '.$done.' kali dijadwalkan)' : ''),
            '/scheduling-simulation',
            $exceeded ? 'warning' : 'info',
        );

        return back()->with('success', $exceeded
            ? 'Jadwal dibatalkan. Berkas sudah dijadwalkan '.$done.' kali — mohon ditinjau.'
            : 'Jadwal dibatalkan, berkas menunggu penjadwalan ulang.');
    }

    /** Membatalkan pengajuan (dipakai bila penjadwalan sudah lewat batas wajar). */
    public function void(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        if (! in_array($loanApplication->status, self::OPEN_STATUSES, true)) {
            return back()->with('error', 'Berkas ini tidak berada pada tahap penjadwalan.');
        }

        $data = $request->validate(
            ['reason' => ['required', 'string', 'max:255']],
            [],
            ['reason' => 'alasan pembatalan'],
        );

        LoanSchedule::create([
            'loan_application_id' => $loanApplication->id,
            'sequence' => $this->scheduleCount($loanApplication),
            'action' => LoanSchedule::ACTION_VOID,
            'survey_date' => $loanApplication->survey_date,
            'surveyor_id' => $loanApplication->surveyor_id,
            'surveyor_name' => $loanApplication->surveyor?->name,
            'reason' => $data['reason'],
            'created_by' => $request->user()->name,
        ]);

        $loanApplication->update([
            'status' => 'DIBATALKAN',
            'surveyor_id' => null,
            'survey_date' => null,
        ]);

        ActivityLog::record(
            "Membatalkan pengajuan {$loanApplication->application_code}",
            self::LABEL,
            'warning',
            $loanApplication,
        );

        Notify::toPermission(
            'loan-simulation.manage',
            'Pengajuan dibatalkan',
            self::LABEL,
            "Berkas {$loanApplication->application_code}: {$data['reason']}",
            '/loan-simulation',
            'warning',
        );

        return back()->with('success', 'Pengajuan dibatalkan.');
    }

    /** Jumlah jadwal yang sudah dibuat (jadwal pertama + jadwal ulang). */
    private function scheduleCount(LoanApplication $r): int
    {
        return $r->schedules()
            ->whereIn('action', [LoanSchedule::ACTION_SCHEDULE, LoanSchedule::ACTION_RESCHEDULE])
            ->count();
    }

    private function isWalkIn(LoanApplication $r): bool
    {
        return $r->product?->alias === self::WALK_IN_PRODUCT;
    }

    /** Peranan surveyor untuk tahap berikutnya (naik setiap kali survei dinilai kurang). */
    private function ladderRole(LoanApplication $r): string
    {
        $done = LoanSurvey::where('loan_application_id', $r->id)->count();

        return self::LADDER[min($done, count(self::LADDER) - 1)];
    }

    /**
     * Daftar surveyor sesuai konteks berkas:
     * - Produk KTA → Kepala Kantor Kas / Customer Service / Teller di kantor yang sama.
     * - Lainnya → peranan sesuai tangga survei (Staff Analis → Kasi → Kabag → Direksi).
     */
    private function surveyors(LoanApplication $r): array
    {
        $query = User::query()->orderBy('name');

        if ($this->isWalkIn($r)) {
            $query->whereHas('roles', fn ($q) => $q->whereIn('name', self::WALK_IN_ROLES))
                ->when($r->office, fn ($q) => $q->where('office', $r->office->name));
        } else {
            $query->whereHas('roles', fn ($q) => $q->where('name', $this->ladderRole($r)));
        }

        return $query->get(['id', 'name'])
            ->map(fn (User $u) => ['value' => $u->id, 'label' => $u->name])
            ->all();
    }

    private function row(LoanApplication $r): array
    {
        $done = $this->scheduleCount($r);

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
            'surveyor_id' => $r->surveyor_id,
            'surveyor_name' => $r->surveyor?->name,
            'survey_date' => $r->survey_date?->format('Y-m-d'),
            'survey_date_label' => $r->survey_date?->translatedFormat('d M Y'),
            'requested_amount' => $r->requested_amount,
            'requested_tenor' => $r->requested_tenor,
            'schedule_count' => $done,
            'over_limit' => $done >= LoanSchedule::MAX_SCHEDULES,
            'survey_count' => LoanSurvey::where('loan_application_id', $r->id)->count(),
            'walk_in' => $this->isWalkIn($r),
            'resurvey' => $r->status === 'SURVEY',
            'surveyor_role' => $this->isWalkIn($r) ? 'Petugas Kantor (KTA)' : $this->ladderRole($r),
            'surveyor_options' => $this->surveyors($r),
            'history' => $r->schedules()->orderByDesc('id')->get()->map(fn (LoanSchedule $s) => [
                'id' => $s->id,
                'sequence' => $s->sequence,
                'action' => $s->action,
                'survey_date' => $s->survey_date?->translatedFormat('d M Y'),
                'surveyor_name' => $s->surveyor_name,
                'note' => $s->note,
                'reason' => $s->reason,
                'created_by' => $s->created_by,
                'created_at' => $s->created_at?->translatedFormat('d M Y H:i'),
            ])->all(),
        ];
    }
}
