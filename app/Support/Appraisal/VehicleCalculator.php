<?php

namespace App\Support\Appraisal;

/**
 * The arithmetic of the vehicle form: the indicated price of each comparable after its adjustments, the market value after the repair
 * cost, the haircut that its seven factors add up to, the liquidation value, and loan-to-value.
 */
final class VehicleCalculator
{
    /**
     * @param  array<string, string>  $values
     */
    public static function run(array $values, AppraisalContext $context): Computation
    {
        $cells = [];
        $indicated = ComparableValues::indicated($values, 'comparables.price', VehicleTemplate::FACTORS, $cells);
        $suggested = ComparableValues::average($indicated);

        $repair = 0;
        foreach (VehicleTemplate::REPAIR_ROWS as $row) {
            $repair += Amounts::whole($values["repair.$row.cost"] ?? null) ?? 0;
        }

        $percents = array_map(fn (string $row): ?float => Amounts::decimal($values["repair.$row.percent"] ?? null), VehicleTemplate::HAIRCUT_ROWS);
        $filled = array_filter($percents, fn (?float $percent): bool => $percent !== null);
        $haircut = $filled === [] ? null : round(min(100.0, max(0.0, array_sum($filled))), 2);

        $selected = Amounts::whole($values['reconciliation.selected'] ?? null) ?? 0;
        $market = max(0, $selected - $repair);
        $liquidation = Haircut::liquidation($market, $haircut);

        $cells += [
            'reconciliation.indicative_1' => $cells['adjustments.indicative.c1'],
            'reconciliation.indicative_2' => $cells['adjustments.indicative.c2'],
            'reconciliation.indicative_3' => $cells['adjustments.indicative.c3'],
            'reconciliation.suggested' => $suggested,
            'repair.total_cost' => $repair,
            'repair.total_haircut' => $haircut,
            'reconciliation.repair' => $repair,
            'reconciliation.market_value' => $market,
            'reconciliation.haircut' => $haircut,
            'reconciliation.liquidation_value' => $liquidation,
        ];

        return new Computation($market, $liquidation, $cells + LoanToValue::figures($values, $context, $market, $liquidation, VehicleTemplate::STRESS), $haircut);
    }
}
