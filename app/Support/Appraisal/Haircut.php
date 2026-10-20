<?php

namespace App\Support\Appraisal;

/**
 * Liquidation value: the market value after the haircut the appraiser sets.
 */
final class Haircut
{
    public static function liquidation(int $market, ?float $percent): int
    {
        return $percent === null ? 0 : Amounts::after($market, min(100.0, max(0.0, $percent)));
    }
}
