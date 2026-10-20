<?php

namespace App\Support\Appraisal;

use App\Models\CollateralAppraisal;
use Illuminate\Http\RedirectResponse;

/**
 * A finalised appraisal is history: every way of changing it is refused with the same sentence.
 */
final class AppraisalGuard
{
    public static function refuseIfFinal(CollateralAppraisal $appraisal): ?RedirectResponse
    {
        return $appraisal->isFinal() ? back()->with('error', 'Penilaian sudah final dan tidak bisa diubah. Mulai penilaian baru untuk memperbarui nilainya.') : null;
    }
}
