<?php

namespace App\Support;

/**
 * A reference row that is also held where no relation can count it (a list of ids kept in a JSON column).
 * The reference screen adds these counts to its "Dipakai" column and refuses to delete the row while any is above zero.
 */
interface CountsExtraUsage
{
    /**
     * Counted for a whole page of rows at once, so the list does not ask the database once per row.
     *
     * @param  list<int>  $ids
     * @return array<int, array<string, int>> by row id, what holds the row by noun (for example "pilihan metode produk")
     */
    public static function extraUsageFor(array $ids): array;
}
