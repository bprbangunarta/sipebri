<?php

namespace App\Support\Appraisal;

/**
 * Loan-to-value and the stress scenarios: how far the value of the collateral may fall before the ceiling is no longer covered.
 * A scenario meets the requirement ("M") while the ceiling is at most the value that is left.
 */
final class LoanToValue
{
    public const MEETS = 'M';

    public const FAILS = 'TM';

    /**
     * @param  array<string, float>  $scenarios  scenario row key => percentage the value falls by
     * @return array<string, int|float|string|null>
     */
    public static function cells(int $ceiling, int $liquidation, array $scenarios, ?int $executionCost): array
    {
        $cells = [
            'ltv.ltv' => Amounts::ratio($ceiling, $liquidation),
            'ltv.coverage' => $ceiling > 0 ? Amounts::ratio($liquidation, $ceiling) : null,
        ];

        foreach ($scenarios as $row => $fall) {
            $cells += self::scenario($row, $ceiling, Amounts::after($liquidation, $fall), $liquidation > 0);
        }

        $left = max(0, $liquidation - ($executionCost ?? 0));

        return $cells + self::scenario('exec', $ceiling, $left, $liquidation > 0 && $executionCost !== null);
    }

    /**
     * @return array<string, int|float|string|null>
     */
    private static function scenario(string $row, int $ceiling, int $value, bool $known): array
    {
        $ltv = $known ? Amounts::ratio($ceiling, $value) : null;

        return [
            "ltv.$row.value" => $known ? $value : null,
            "ltv.$row.ltv" => $ltv,
            "ltv.$row.status" => $known ? (($ltv !== null && $ltv <= 100.0) ? self::MEETS : self::FAILS) : null,
        ];
    }
}
