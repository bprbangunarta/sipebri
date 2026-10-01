<?php

namespace App\Support\CreditAnalysis;

use App\Enums\LoanStatus;
use App\Models\LoanAnalysis;
use App\Models\LoanApplication;
use App\Models\User;

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

    /**
     * The analysis to change. It is created at the first change, and a file that was only surveyed moves to the
     * analysis stage then.
     */
    public static function edit(User $user, LoanApplication $loan): LoanAnalysis
    {
        abort_unless($loan->surveyor_id === $user->id, 403, 'Berkas ini bukan penugasan Anda.');
        abort_unless(in_array($loan->status, self::EDITABLE, true), 403, 'Berkas ini tidak berada pada tahap analisa.');

        return LoanAnalysis::begin($loan);
    }
}
