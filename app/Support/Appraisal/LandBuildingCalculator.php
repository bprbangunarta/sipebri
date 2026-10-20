<?php

namespace App\Support\Appraisal;

/**
 * The arithmetic of the land and building form: indicated land value per m² from the comparables, the cost approach for the
 * buildings, the reconciliation to a market value, the haircut to a liquidation value, and loan-to-value.
 */
final class LandBuildingCalculator
{
    private const COMPARABLES = ['c1', 'c2', 'c3'];

    private const FACTORS = ['time', 'location', 'shape', 'right', 'zoning', 'building', 'position', 'offer'];

    private const BUILDINGS = ['main', 'additional'];

    private const FACILITIES = ['canopy', 'fence', 'facilities', 'other'];

    /**
     * @param  array<string, string>  $values
     */
    public static function run(array $values, AppraisalContext $context): Computation
    {
        $cells = [];
        self::comparables($values, $cells);
        $landMarket = self::landMarket($values, $context);
        [$building, $facilities] = self::cost($values, $cells);

        $selected = [
            'land' => Amounts::whole($values['reconciliation.land.selected'] ?? null) ?? 0,
            'building' => Amounts::whole($values['reconciliation.building.selected'] ?? null) ?? 0,
            'facilities' => Amounts::whole($values['reconciliation.facilities.selected'] ?? null) ?? 0,
        ];
        $deductions = Amounts::whole($values['reconciliation.deductions.selected'] ?? null) ?? 0;

        // The approaches that do not apply to a line are empty, so the grid has a figure (or none) for every cell it shows.
        $cells['reconciliation.land.cost'] = $cells['reconciliation.building.market'] = $cells['reconciliation.facilities.market'] = null;
        $cells['reconciliation.deductions.market'] = $cells['reconciliation.deductions.cost'] = null;
        $cells['reconciliation.land.market'] = $landMarket;
        $cells['reconciliation.building.cost'] = $building;
        $cells['reconciliation.facilities.cost'] = $facilities;
        $cells['reconciliation.total.market'] = $landMarket;
        $cells['reconciliation.total.cost'] = $building + $facilities;
        $cells['reconciliation.total.selected'] = array_sum($selected) - $deductions;

        $market = max(0, array_sum($selected) - $deductions);
        $liquidation = Haircut::liquidation($market, Amounts::decimal($values['h:haircut_percent'] ?? null));

        $cells['reconciliation.market_value'] = $market;
        $cells['reconciliation.liquidation_value'] = $liquidation;

        return new Computation($market, $liquidation, $cells + self::summary($values, $context, $market, $liquidation));
    }

    /**
     * The indicated land value per m² of each comparable after its adjustments, and the range and average of them.
     *
     * @param  array<string, string>  $values
     * @param  array<string, int|float|string|null>  $cells
     */
    private static function comparables(array $values, array &$cells): void
    {
        $indicated = [];

        foreach (self::COMPARABLES as $column) {
            $total = 0.0;
            $any = false;

            foreach (self::FACTORS as $factor) {
                $adjustment = Amounts::decimal($values["adjustments.$factor.$column"] ?? null);
                $any = $any || $adjustment !== null;
                $total += $adjustment ?? 0.0;
            }

            $price = Amounts::whole($values["comparables.land_per_m2.$column"] ?? null);
            $cells["adjustments.total.$column"] = $any ? round($total, 2) : null;
            $cells["adjustments.indicative.$column"] = $price === null ? null : (int) round($price * (1 + $total / 100));

            if ($price !== null) {
                $indicated[] = $cells["adjustments.indicative.$column"];
            }
        }

        $cells['adjustments.range_min'] = $indicated === [] ? null : min($indicated);
        $cells['adjustments.range_max'] = $indicated === [] ? null : max($indicated);
        $cells['adjustments.suggested'] = $indicated === [] ? null : (int) round(array_sum($indicated) / count($indicated));
    }

    /**
     * @param  array<string, string>  $values
     */
    private static function landMarket(array $values, AppraisalContext $context): int
    {
        $perM2 = Amounts::whole($values['adjustments.selected'] ?? null);
        $area = Amounts::whole($values['land_physical.area'] ?? null) ?? $context->certificateLandArea;

        return $perM2 !== null && $area !== null ? $perM2 * $area : 0;
    }

    /**
     * The present value of the buildings and of the facilities, after depreciation.
     *
     * @param  array<string, string>  $values
     * @param  array<string, int|float|string|null>  $cells
     * @return array{0: int, 1: int}
     */
    private static function cost(array $values, array &$cells): array
    {
        $general = min(100.0, max(0.0, array_sum(array_map(
            fn (string $kind): float => Amounts::decimal($values["cost.$kind"] ?? null) ?? 0.0,
            ['physical', 'functional', 'external'],
        ))));
        $cells['cost.total'] = round($general, 2);

        $present = [];

        foreach ([...self::BUILDINGS, ...self::FACILITIES] as $row) {
            $area = Amounts::whole($values["cost.$row.area"] ?? null);
            $unit = Amounts::whole($values["cost.$row.unit_cost"] ?? null);
            $depreciation = min(100.0, max(0.0, Amounts::decimal($values["cost.$row.depreciation"] ?? null) ?? $general));
            $new = $area !== null && $unit !== null ? $area * $unit : null;

            $cells["cost.$row.new_cost"] = $new;
            $cells["cost.$row.present"] = $new === null ? null : Amounts::after($new, $depreciation);
            $present[$row] = $cells["cost.$row.present"] ?? 0;
        }

        return [
            array_sum(array_intersect_key($present, array_flip(self::BUILDINGS))),
            array_sum(array_intersect_key($present, array_flip(self::FACILITIES))),
        ];
    }

    /**
     * Loan-to-value, the stress scenarios, and the figures repeated in the conclusion.
     *
     * @param  array<string, string>  $values
     * @return array<string, int|float|string|null>
     */
    private static function summary(array $values, AppraisalContext $context, int $market, int $liquidation): array
    {
        $loanToValue = LoanToValue::cells($context->ceiling, $liquidation, LandBuildingTemplate::STRESS, Amounts::whole($values['ltv.execution_cost'] ?? null));

        return $loanToValue + [
            'ltv.ceiling' => $context->ceiling,
            'ltv.liquidation' => $liquidation,
            'conclusion.market_value' => $market,
            'conclusion.liquidation_value' => $liquidation,
            'conclusion.ltv' => $loanToValue['ltv.ltv'],
            'conclusion.coverage' => $loanToValue['ltv.coverage'],
        ];
    }
}
