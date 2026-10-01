<?php

namespace App\Http\Controllers;

use App\Enums\LoanStatus;
use App\Models\AnalysisCorrection;
use App\Models\LoanAnalysis;
use App\Models\LoanApplication;
use App\Models\User;
use App\Support\Notify;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Corrections of the analysis of an approved file. The analyst asks, the section head of the file opens (or opens one
 * without being asked); the approval itself is not touched and the figures it rests on are guarded while it is open
 * (see BaseFigures). A correction is closed with a note, and all of them are kept.
 */
class AnalysisCorrectionController extends Controller
{
    /** The analyst asks for a correction; the section head of the file opens one directly. */
    public function store(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $analysis = $this->approvedAnalysis($loanApplication);
        $supervisor = $loanApplication->supervisor_id === $user->id;
        abort_unless($supervisor || $loanApplication->surveyor_id === $user->id, 403, 'Berkas ini bukan penugasan Anda.');
        abort_if($analysis->corrections()->whereIn('status', [AnalysisCorrection::REQUESTED, AnalysisCorrection::OPEN])->exists(), 422, 'Masih ada koreksi yang berjalan untuk berkas ini.');

        $data = $request->validate(['reason' => ['required', 'string', 'max:500']], [], ['reason' => 'alasan koreksi']);

        $correction = $analysis->corrections()->create([
            'status' => $supervisor ? AnalysisCorrection::OPEN : AnalysisCorrection::REQUESTED,
            'reason' => $data['reason'],
            'requested_by' => $user->id,
            'requested_at' => now(),
            ...($supervisor ? ['opened_by' => $user->id, 'opened_at' => now()] : []),
        ]);

        $code = $loanApplication->application_code;
        $supervisor
            ? $this->tell($loanApplication->surveyor, 'Koreksi analisa dibuka', "Koreksi analisa berkas {$code} dibuka: {$correction->reason}", $loanApplication)
            : $this->tell($loanApplication->supervisor, 'Permintaan koreksi analisa', "{$user->name} meminta koreksi analisa berkas {$code}: {$correction->reason}", $loanApplication);

        return back()->with('success', $supervisor ? 'Koreksi dibuka. Analis bisa mengubah lembar analisa.' : 'Permintaan koreksi dikirim ke Kasi Analis.');
    }

    public function open(Request $request, LoanApplication $loanApplication, AnalysisCorrection $correction): RedirectResponse
    {
        $this->supervisorOnly($request, $loanApplication, $correction, AnalysisCorrection::REQUESTED);

        $correction->update(['status' => AnalysisCorrection::OPEN, 'opened_by' => $request->user()?->id, 'opened_at' => now()]);
        $this->tell($loanApplication->surveyor, 'Koreksi analisa dibuka', "Permintaan koreksi berkas {$loanApplication->application_code} disetujui.", $loanApplication);

        return back()->with('success', 'Koreksi dibuka. Analis bisa mengubah lembar analisa.');
    }

    public function decline(Request $request, LoanApplication $loanApplication, AnalysisCorrection $correction): RedirectResponse
    {
        $this->supervisorOnly($request, $loanApplication, $correction, AnalysisCorrection::REQUESTED);
        $data = $request->validate(['reason' => ['required', 'string', 'max:500']], [], ['reason' => 'alasan penolakan']);

        $correction->update(['status' => AnalysisCorrection::DECLINED, 'resolved_by' => $request->user()?->id, 'resolved_at' => now(), 'resolution_note' => $data['reason']]);
        $this->tell($loanApplication->surveyor, 'Permintaan koreksi ditolak', "Permintaan koreksi berkas {$loanApplication->application_code} ditolak: {$data['reason']}", $loanApplication, 'warning');

        return back()->with('success', 'Permintaan koreksi ditolak.');
    }

    /** The analyst (or the section head) finishes the correction. */
    public function close(Request $request, LoanApplication $loanApplication, AnalysisCorrection $correction): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $this->approvedAnalysis($loanApplication);
        abort_unless($correction->loanAnalysis->loan_application_id === $loanApplication->id, 404);
        abort_unless($loanApplication->surveyor_id === $user->id || $loanApplication->supervisor_id === $user->id, 403, 'Berkas ini bukan penugasan Anda.');
        abort_unless($correction->status === AnalysisCorrection::OPEN, 422, 'Koreksi ini tidak sedang dibuka.');

        $data = $request->validate(['reason' => ['required', 'string', 'max:500']], [], ['reason' => 'catatan koreksi']);

        $correction->update(['status' => AnalysisCorrection::CLOSED, 'resolved_by' => $user->id, 'resolved_at' => now(), 'resolution_note' => $data['reason']]);

        $other = $user->id === $loanApplication->surveyor_id ? $loanApplication->supervisor : $loanApplication->surveyor;
        $this->tell($other, 'Koreksi analisa selesai', "Koreksi analisa berkas {$loanApplication->application_code} selesai: {$data['reason']}", $loanApplication, 'success');

        return back()->with('success', 'Koreksi selesai. Lembar analisa terkunci kembali.');
    }

    private function approvedAnalysis(LoanApplication $loan): LoanAnalysis
    {
        abort_unless($loan->status === LoanStatus::Approved, 422, 'Koreksi hanya untuk berkas yang sudah disetujui dan belum dicairkan.');

        return $loan->analysis ?? abort(404);
    }

    private function supervisorOnly(Request $request, LoanApplication $loan, AnalysisCorrection $correction, string $expected): void
    {
        $this->approvedAnalysis($loan);
        abort_unless($correction->loanAnalysis->loan_application_id === $loan->id, 404);
        abort_unless($loan->supervisor_id === $request->user()?->id, 403, 'Hanya Kasi Analis berkas ini yang bisa membuka atau menolak koreksi.');
        abort_unless($correction->status === $expected, 422, 'Status koreksi sudah berubah.');
    }

    private function tell(?User $person, string $title, string $body, LoanApplication $loan, string $level = 'info'): void
    {
        if ($person !== null) {
            Notify::toUser($person, $title, 'Analisa Kredit', $body, "/credit-analysis/{$loan->id}", $level);
        }
    }
}
