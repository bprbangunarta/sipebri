<?php

namespace App\Support\Appraisal;

use App\Models\CollateralAppraisal;
use Illuminate\Support\Carbon;

/**
 * The answers of an appraisal as one list by item key: the entries, and the columns of the appraisal under `h:<column>`.
 */
final class AppraisalValues
{
    /**
     * @return array<string, string>
     */
    public static function of(CollateralAppraisal $appraisal): array
    {
        $values = $appraisal->entries->mapWithKeys(fn ($entry): array => [$entry->item => (string) $entry->value])->filter(fn (string $value): bool => $value !== '')->all();

        foreach (CollateralAppraisal::HEADER_FIELDS as $column) {
            $value = $appraisal->getAttribute($column);

            if ($value !== null && $value !== '') {
                $values["h:$column"] = $value instanceof Carbon ? $value->toDateString() : (string) $value;
            }
        }

        return $values;
    }

    /**
     * @return array<string, string>
     */
    public static function notes(CollateralAppraisal $appraisal): array
    {
        return $appraisal->entries->filter(fn ($entry): bool => filled($entry->note))->mapWithKeys(fn ($entry): array => [$entry->item => (string) $entry->note])->all();
    }

    /** What the screen and the calculation need besides the answers. */
    public static function context(CollateralAppraisal $appraisal): AppraisalContext
    {
        return new AppraisalContext((int) $appraisal->credit_ceiling, $appraisal->collateral->certificate_land_area);
    }
}
