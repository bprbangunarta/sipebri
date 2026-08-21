<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\LoanApplication;
use App\Models\LoanSchedule;
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

    /** Berkas yang boleh dijadwalkan. */
    private const OPEN_STATUSES = ['DIAJUKAN', 'PENJADWALAN'];

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
            'surveyorOptions' => $this->surveyors(),
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

        if ($done >= LoanSchedule::MAX_SCHEDULES) {
            return back()->with('error', 'Batas penjadwalan '.LoanSchedule::MAX_SCHEDULES.' kali sudah tercapai.');
        }

        $data = $request->validate([
            'survey_date' => ['required', 'date', 'after_or_equal:today'],
            'surveyor_id' => ['required', 'integer', Rule::in(collect($this->surveyors())->pluck('value')->all())],
            'note' => ['nullable', 'string', 'max:255'],
        ], [], ['survey_date' => 'tanggal survei', 'surveyor_id' => 'staff analis', 'note' => 'catatan']);

        $surveyor = User::findOrFail($data['surveyor_id']);

        LoanSchedule::create([
            'loan_application_id' => $loanApplication->id,
            'sequence' => $done + 1,
            'action' => $done === 0 ? LoanSchedule::ACTION_SCHEDULE : LoanSchedule::ACTION_RESCHEDULE,
            'survey_date' => $data['survey_date'],
            'surveyor_id' => $surveyor->id,
            'surveyor_name' => $surveyor->name,
            'note' => $data['note'] ?? null,
            'created_by' => $request->user()->name,
        ]);

        $loanApplication->update([
            'status' => 'PENJADWALAN',
            'surveyor_id' => $surveyor->id,
            'survey_date' => $data['survey_date'],
        ]);

        $label = $done === 0 ? 'Menjadwalkan' : 'Menjadwalkan ulang';
        ActivityLog::record(
            "{$label} survei berkas {$loanApplication->application_code}",
            self::LABEL,
            'success',
            $loanApplication,
        );

        Notify::toUser(
            $surveyor,
            'Penugasan survei',
            self::LABEL,
            "Berkas {$loanApplication->application_code} ({$loanApplication->full_name}) disurvei "
                .$loanApplication->survey_date->translatedFormat('d M Y').'.',
            '/survey-simulation',
        );

        return back()->with('success', 'Jadwal survei disimpan.');
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

        $exhausted = $done >= LoanSchedule::MAX_SCHEDULES;

        $loanApplication->update([
            'status' => $exhausted ? 'DRAFT' : 'DIAJUKAN',
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
            $exhausted ? 'Penjadwalan mentok batas' : 'Permintaan penjadwalan ulang',
            self::LABEL,
            "Berkas {$loanApplication->application_code}: {$data['reason']}",
            '/scheduling-simulation',
            $exhausted ? 'warning' : 'info',
        );

        return back()->with('success', $exhausted
            ? 'Batas penjadwalan habis, berkas dikembalikan ke DRAFT.'
            : 'Jadwal dibatalkan, berkas menunggu penjadwalan ulang.');
    }

    /** Jumlah jadwal yang sudah dibuat (jadwal pertama + jadwal ulang). */
    private function scheduleCount(LoanApplication $r): int
    {
        return $r->schedules()
            ->whereIn('action', [LoanSchedule::ACTION_SCHEDULE, LoanSchedule::ACTION_RESCHEDULE])
            ->count();
    }

    /** Staff analis yang boleh ditugaskan survei. */
    private function surveyors(): array
    {
        return User::query()
            ->whereHas('roles', fn ($q) => $q->where('name', 'Staff Analis'))
            ->orderBy('name')
            ->get(['id', 'name'])
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
            'can_schedule' => $done < LoanSchedule::MAX_SCHEDULES,
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
