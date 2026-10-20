<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

/**
 * What holds the rows of one reference table, for the "Dipakai" column and the delete guard: the relation counts that came with
 * the rows plus the holders no relation can count. One instance serves one request.
 */
class ReferenceUsage
{
    /** @var array<int, array<string, int>> holders no relation can count, by row id, counted once per request */
    private array $extraUsage = [];

    public function __construct(private readonly ReferenceResource $resource) {}

    /**
     * Counts the holders of a whole page of rows that no relation can count, in one go, before the rows are shown or sorted.
     *
     * @param  iterable<Model>  $records
     */
    public function loadExtraUsage(iterable $records): void
    {
        $model = $this->resource->model();

        if (! is_a($model, CountsExtraUsage::class, true)) {
            return;
        }

        $ids = array_values(array_map(fn (Model $record): int => (int) $record->getKey(), [...$records]));
        $this->extraUsage = $model::extraUsageFor($ids) + $this->extraUsage;
    }

    /**
     * Holders of this row that no relation can count (ids kept in a JSON column), by noun, only those above zero.
     *
     * @return array<string, int>
     */
    public function extraUsage(Model $record): array
    {
        if (! $record instanceof CountsExtraUsage) {
            return [];
        }

        $this->extraUsage[$record->getKey()] ??= $record::extraUsageFor([(int) $record->getKey()])[(int) $record->getKey()] ?? [];

        return array_filter($this->extraUsage[$record->getKey()], fn (int $count): bool => $count > 0);
    }

    /**
     * Everything that holds a row, for the "Dipakai" column: the relation counts loaded with the rows plus the holders no relation can count.
     */
    public function usageTotal(Model $record): int
    {
        $relations = array_map(fn (string $relation): int => (int) $record->getAttribute(Str::snake($relation).'_count'), array_keys($this->resource->usage()));

        return array_sum($relations) + array_sum($this->extraUsage($record));
    }

    /**
     * The "Dipakai" column adds up several relations, which one SQL ORDER BY cannot do without building SQL text from names;
     * lookup tables that are used by other records stay small, so this one order is done on the rows in memory.
     *
     * @param  Builder<Model>  $query
     * @param  array<string, mixed>  $queryString
     * @return LengthAwarePaginator<int, Model>
     */
    public function paginateByUsage(Builder $query, string $direction, int $perPage, string $path, array $queryString): LengthAwarePaginator
    {
        $first = $this->resource->rawFields()[0]['name'];
        $records = $query->get();
        $this->loadExtraUsage($records);

        $total = fn (Model $record): int => $this->usageTotal($record);
        $rows = $records->sortBy([
            fn (Model $a, Model $b): int => $direction === 'asc' ? $total($a) <=> $total($b) : $total($b) <=> $total($a),
            fn (Model $a, Model $b): int => $a->getAttribute($first) <=> $b->getAttribute($first),
            fn (Model $a, Model $b): int => $a->getKey() <=> $b->getKey(),
        ])->values();
        $page = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator($rows->forPage($page, $perPage)->values(), $rows->count(), $perPage, $page, ['path' => $path, 'query' => $queryString]);
    }
}
