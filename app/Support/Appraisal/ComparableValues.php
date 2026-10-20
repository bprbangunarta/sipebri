<?php

namespace App\Support\Appraisal;

/**
 * The three market comparables of a form: each one's price after the adjustments the appraiser makes per factor, and the figures
 * that follow from them.
 */
final class ComparableValues
{
    public const COLUMNS = ['c1', 'c2', 'c3'];

    /**
     * Writes the total adjustment and the indicated value of each comparable into the cells.
     *
     * @param  array<string, string>  $values
     * @param  list<string>  $factors  row keys of the adjustments grid
     * @param  array<string, int|float|string|null>  $cells
     * @return list<int> the indicated values of the comparables that have a price
     */
    public static function indicated(array $values, string $priceItem, array $factors, array &$cells): array
    {
        $indicated = [];

        foreach (self::COLUMNS as $column) {
            $total = 0.0;
            $any = false;

            foreach ($factors as $factor) {
                $adjustment = Amounts::decimal($values["adjustments.$factor.$column"] ?? null);
                $any = $any || $adjustment !== null;
                $total += $adjustment ?? 0.0;
            }

            $price = Amounts::whole($values["$priceItem.$column"] ?? null);
            $cells["adjustments.total.$column"] = $any ? round($total, 2) : null;
            $cells["adjustments.indicative.$column"] = $price === null ? null : (int) round($price * (1 + $total / 100));

            if ($price !== null) {
                $indicated[] = $cells["adjustments.indicative.$column"];
            }
        }

        return $indicated;
    }

    /**
     * @param  list<int>  $indicated
     */
    public static function average(array $indicated): ?int
    {
        return $indicated === [] ? null : (int) round(array_sum($indicated) / count($indicated));
    }
}
