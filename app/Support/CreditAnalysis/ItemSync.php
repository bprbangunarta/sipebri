<?php

namespace App\Support\CreditAnalysis;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Replaces the rows of a list (the goods of a trade, the obligations of a household) with what was submitted. Rows are only
 * rewritten when something changed, so saving a form that was not touched leaves nothing in the audit trail.
 */
final class ItemSync
{
    /**
     * @param  HasMany<covariant Model, covariant Model>  $relation  the rows of the parent
     * @param  list<string>  $groups  the groups this submission covers; rows of other groups stay
     * @param  array<array-key, array<string, mixed>>  $submitted  rows with a `group` (when several groups are covered) and the fields in `$fields`
     * @param  list<string>  $fields  columns to keep besides `group` and `sort`; `name` is stored in capitals
     */
    public static function replace(HasMany $relation, array $groups, array $submitted, array $fields): void
    {
        $normalise = static function (array $row, int $sort) use ($fields): array {
            $out = ['group' => $row['group'], 'sort' => $sort];

            foreach ($fields as $field) {
                $value = $row[$field] ?? null;
                $out[$field] = $field === 'name' ? Str::upper((string) $value) : ($value ?? 0);
            }

            return $out;
        };

        $wanted = [];
        foreach (array_values($submitted) as $sort => $row) {
            $wanted[] = $normalise($row, $sort);
        }

        $existing = $relation->get()->filter(fn (Model $m): bool => in_array($m->getAttribute('group'), $groups, true));
        $current = $existing->values()->map(fn (Model $m, int $i): array => $normalise($m->getAttributes(), $i))->all();

        if ($current == $wanted) { // loose: 3 and 3.00 are the same quantity
            return;
        }

        $existing->each(fn (Model $m) => $m->delete());

        foreach ($wanted as $row) {
            $relation->create($row);
        }
    }
}
