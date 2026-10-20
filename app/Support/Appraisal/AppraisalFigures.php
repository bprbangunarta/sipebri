<?php

namespace App\Support\Appraisal;

use App\Models\CollateralAppraisal;

/**
 * Works out the figures of an appraisal from its answers and keeps the ones lists and the credit analysis read on the appraisal.
 */
final class AppraisalFigures
{
    public static function refresh(CollateralAppraisal $appraisal, AppraisalTemplate $template): Computation
    {
        $appraisal->load('entries', 'collateral');
        $computation = $template->compute(AppraisalValues::of($appraisal), AppraisalValues::context($appraisal));

        $appraisal->update([
            'market_value' => $computation->marketValue,
            'liquidation_value' => $computation->liquidationValue,
            ...($computation->haircut === null ? [] : ['haircut_percent' => $computation->haircut]),
        ]);

        return $computation;
    }
}
