<?php

namespace App\Support\CreditAnalysis;

use App\Enums\LoanStatus;
use App\Models\LoanAnalysis;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Who may open and change the analysis of a file. The surveyor assigned to the file does the analysis; the section head
 * of the file may read it. Changing it is only possible while the file is at the analysis stage.
 */
final class AnalysisAccess
{
    /** Stages at which the worksheet is filled in. */
    private const EDITABLE = [LoanStatus::Survey, LoanStatus::Analysis];

    public static function view(User $user, LoanApplication $loan): void
    {
        $atCommittee = in_array($loan->status, [LoanStatus::Committee, LoanStatus::Approved, LoanStatus::Rejected, LoanStatus::Cancelled], true);

        // Whoever decides the file may read the worksheet once it is with the committee.
        $committeeReader = $atCommittee && $user->can('approvals.view');

        abort_unless($loan->surveyor_id === $user->id || $loan->supervisor_id === $user->id || $committeeReader, 403, 'Berkas ini bukan penugasan Anda.');
        abort_unless(in_array($loan->status, self::EDITABLE, true) || $atCommittee, 403, 'Berkas ini tidak berada pada tahap analisa.');
    }

    /** Whether the worksheet is open for this person to change: their file at the analysis stage, or an approved one with a correction open. */
    public static function canEdit(User $user, LoanApplication $loan): bool
    {
        if ($loan->surveyor_id !== $user->id) {
            return false;
        }

        return in_array($loan->status, self::EDITABLE, true) || ($loan->status === LoanStatus::Approved && $loan->analysis?->openCorrection() !== null);
    }

    /**
     * The analysis to change. It is created at the first change, and a file that was only surveyed moves to the
     * analysis stage then. A file that was approved can only be changed while a correction is open.
     */
    public static function edit(User $user, LoanApplication $loan): LoanAnalysis
    {
        abort_unless($loan->surveyor_id === $user->id, 403, 'Berkas ini bukan penugasan Anda.');

        if ($loan->status === LoanStatus::Approved) {
            $analysis = $loan->analysis;

            abort_unless($analysis?->openCorrection() !== null, 403, 'Berkas ini sudah disetujui. Minta Kasi Analis membuka koreksi untuk mengubahnya.');

            return $analysis;
        }

        abort_unless(in_array($loan->status, self::EDITABLE, true), 403, 'Berkas ini tidak berada pada tahap analisa.');

        return LoanAnalysis::begin($loan);
    }

    /**
     * Run a change of the worksheet. On an approved file (a correction is open) the change is undone, and the reason
     * returned, when it alters a figure the committee decided on.
     *
     * @param  callable(LoanAnalysis): mixed  $work
     */
    public static function change(User $user, LoanApplication $loan, callable $work): ?string
    {
        $analysis = self::edit($user, $loan);

        if ($loan->status !== LoanStatus::Approved) {
            $work($analysis);

            return null;
        }

        try {
            DB::transaction(function () use ($analysis, $work): void {
                $before = BaseFigures::snapshot($analysis);
                $work($analysis);

                if ($changed = BaseFigures::changes($before, BaseFigures::snapshot($analysis))) {
                    throw new BaseFiguresChanged($changed);
                }
            });
        } catch (BaseFiguresChanged $changed) {
            return 'Perubahan dibatalkan: angka yang sudah disetujui komite tidak boleh berubah ('.implode(', ', $changed->figures).'). Perubahan itu perlu persetujuan ulang.';
        }

        return null;
    }
}
