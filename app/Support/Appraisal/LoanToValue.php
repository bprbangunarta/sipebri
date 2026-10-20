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
     * Loan-to-value and the figures the conclusion repeats, for a form whose scenarios are the given ones.
     *
     * @param  array<string, string>  $values
     * @param  array<string, float>  $scenarios
     * @return array<string, int|float|string|null>
     */
    public static function figures(array $values, AppraisalContext $context, int $market, int $liquidation, array $scenarios): array
    {
        $cells = self::cells($context->ceiling, $liquidation, $scenarios, Amounts::whole($values['ltv.execution_cost'] ?? null));

        return $cells + [
            'ltv.ceiling' => $context->ceiling,
            'ltv.liquidation' => $liquidation,
            'conclusion.market_value' => $market,
            'conclusion.liquidation_value' => $liquidation,
            'conclusion.ltv' => $cells['ltv.ltv'],
            'conclusion.coverage' => $cells['ltv.coverage'],
        ];
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
