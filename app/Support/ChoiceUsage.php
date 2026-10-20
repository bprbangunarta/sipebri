<?php

namespace App\Support;

use App\Models\ProductParameter;

/**
 * How many product parameters list each id in one of their JSON choice columns.
 */
class ChoiceUsage
{
    /**
     * @param  list<int>  $ids
     * @return array<int, int> by id, only the ids that were asked for
     */
    public static function count(string $column, array $ids): array
    {
        $tally = array_fill_keys($ids, 0);

        foreach (ProductParameter::query()->pluck($column) as $chosen) {
            foreach (array_unique((array) $chosen) as $id) {
                if (isset($tally[$id])) {
                    $tally[$id]++;
                }
            }
        }

        return $tally;
    }
}
