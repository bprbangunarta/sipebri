<?php

namespace App\Support;

/**
 * A reference row that is also held where no relation can count it (a list of ids kept in a JSON column).
 * The reference screen adds these counts to its "Dipakai" column and refuses to delete the row while any is above zero.
 */
interface CountsExtraUsage
{
    /**
     * @return array<string, int> what holds this row, by noun (for example "parameter produk"), only the counts above zero need to be present
     */
    public function extraUsage(): array;
}
