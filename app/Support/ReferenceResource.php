<?php

namespace App\Support;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

/**
 * Typed view over one entry of config/references.php.
 */
class ReferenceResource
{
    /** Sort key of the "Dipakai" column (how many records refer to a row). */
    public const USAGE_SORT = 'usage_count';

    /**
     * @param  array<string, mixed>  $config
     */
    private function __construct(public readonly string $slug, private readonly array $config) {}

    public static function find(string $slug): self
    {
        $config = config("references.$slug");
        abort_unless(is_array($config), 404);

        return new self($slug, $config);
    }

    /**
     * @return array<int, self>
     */
    public static function all(): array
    {
        $all = [];

        foreach (array_keys(config('references')) as $slug) {
            $all[] = self::find((string) $slug);
        }

        return $all;
    }

    public function label(): string
    {
        return $this->config['label'];
    }

    public function section(): string
    {
        return $this->config['section'];
    }

    public function group(): string
    {
        return $this->config['group'];
    }

    /**
     * @return class-string<Model>
     */
    public function model(): string
    {
        return $this->config['model'];
    }

    /**
     * @return array<string, string> relation => noun
     */
    public function usage(): array
    {
        return $this->config['usage'];
    }

    /**
     * @return list<string> every column of the table the list may be sorted by: each field, and the usage count when the table tracks it
     */
    public function sortKeys(): array
    {
        return [...array_column($this->rawFields(), 'name'), ...($this->usage() === [] ? [] : [self::USAGE_SORT])];
    }

    /**
     * The usage relations with rows kept by soft delete included: a deactivated person or a deleted application still holds the foreign key.
     *
     * @return array<string, Closure(Builder<Model>|Relation<Model, Model, mixed>): mixed> constraints keyed by relation, for `withCount`
     */
    public function usageCounts(): array
    {
        $record = new ($this->model());
        $counts = [];

        foreach (array_keys($this->usage()) as $relation) {
            $counts[$relation] = $this->heldByForeignKey($record->{$relation}())
                ? fn ($query) => $query->withTrashed()
                : fn ($query) => $query;
        }

        return $counts;
    }

    /**
     * How many records, deleted ones included, refer to this one through the relation.
     */
    public function usageOf(Model $record, string $relation): int
    {
        $link = $record->{$relation}();

        return $this->heldByForeignKey($link) ? $link->withTrashed()->count() : $link->count();
    }

    /**
     * A soft-deleted row still holds a foreign key and blocks the delete in the database, so it counts. A relation that
     * works through a code (no foreign key) counts live rows only: deleted ones keep no guarantee to protect.
     *
     * @param  Relation<Model, Model, mixed>  $link
     */
    private function heldByForeignKey(Relation $link): bool
    {
        return $link instanceof HasOneOrMany
            && $link->getLocalKeyName() !== 'code'
            && in_array(SoftDeletes::class, class_uses_recursive($link->getRelated()), true);
    }

    /**
     * The relation by which records point at a code that many rows share, when this table has one.
     *
     * @return array<string, string>
     */
    public function sharedCode(): array
    {
        return $this->config['shared_code'] ?? [];
    }

    /**
     * What still points at the code of this row, when it is the last row holding that code: [count, noun], or null when the code may go.
     *
     * @return array{0: int, 1: string}|null
     */
    public function lastHolderOfUsedCode(Model $record): ?array
    {
        $others = $this->model()::query()->where('code', $record->getAttribute('code'))->whereKeyNot($record->getKey())->exists();

        if ($others) {
            return null;
        }

        foreach ($this->sharedCode() as $relation => $noun) {
            $count = $record->{$relation}()->count();

            if ($count > 0) {
                return [$count, $noun];
            }
        }

        return null;
    }

    /**
     * The field whose value names a record in a message: `title` in the config, else `name`, else the first field.
     */
    public function titleField(): string
    {
        $names = array_column($this->rawFields(), 'name');

        return $this->config['title'] ?? (in_array('name', $names, true) ? 'name' : $names[0]);
    }

    public function title(Model $record): string
    {
        return (string) ($record->getAttribute($this->titleField()) ?? $record->getKey());
    }

    public function locksCode(): bool
    {
        return (bool) ($this->config['lock_code'] ?? false);
    }

    public function importable(): bool
    {
        return ($this->config['import'] ?? false) !== false;
    }

    /**
     * @return list<string> labels of the fields, the header row of the import template
     */
    public function importHeader(): array
    {
        return array_column($this->rawFields(), 'label');
    }

    /**
     * @return list<list<string>> rows shown in the import template as examples (one cell per field)
     */
    public function importExamples(): array
    {
        $import = $this->config['import'] ?? [];

        return is_array($import) ? ($import['examples'] ?? []) : [];
    }

    /**
     * @return array<string, string> field name => lowercase label, for validation messages
     */
    public function attributeNames(): array
    {
        return array_column(array_map(fn (array $f): array => ['name' => $f['name'], 'label' => mb_strtolower($f['label'])], $this->rawFields()), 'label', 'name');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function actions(): array
    {
        return $this->config['actions'] ?? [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function rawFields(): array
    {
        return $this->config['fields'];
    }

    /**
     * Field definitions with select options resolved, ready for the frontend.
     *
     * @return array<int, array<string, mixed>>
     */
    public function fields(): array
    {
        return array_map(function (array $field): array {
            if (isset($field['options_from'])) {
                $field['options'] = $this->options($field['options_from']);
            }

            unset($field['rules'], $field['options_from']);

            return $field;
        }, $this->rawFields());
    }

    /**
     * @param  array{0: class-string<Model>, 1: string, 2: array<int, string>}  $source
     * @return array<int, array{value: mixed, label: string}>
     */
    private function options(array $source): array
    {
        [$model, $value, $labels] = $source;

        return $model::query()->orderBy($labels[0])->get()
            ->map(fn (Model $m): array => ['value' => $m->{$value}, 'label' => implode(' – ', array_map(fn (string $c) => (string) $m->{$c}, $labels))])
            ->all();
    }

    /**
     * Normalise raw input (trim, uppercase) before validation.
     *
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function normalize(array $input): array
    {
        $out = [];

        foreach ($this->rawFields() as $field) {
            $value = $input[$field['name']] ?? null;

            if (is_string($value)) {
                $value = trim($value);
                $value = ($field['uppercase'] ?? false) ? mb_strtoupper($value) : $value;
                $value = $value === '' ? null : $value;
            }

            $out[$field['name']] = $value;
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $input  normalised input
     * @return array<string, array<int, mixed>>
     */
    public function rules(?Model $ignore, array $input): array
    {
        $table = $this->tableOf($this->model());
        $rules = [];

        foreach ($this->rawFields() as $field) {
            $column = $field['name'];
            $list = [($field['required'] ?? false) ? 'required' : 'nullable'];

            $list = [...$list, ...match ($field['type']) {
                'number' => ['integer', 'min:'.($field['min'] ?? 0), 'max:'.($field['max'] ?? 999999999999)],
                'boolean' => ['boolean'],
                'select' => [isset($field['options_from'])
                    ? Rule::exists($this->tableOf($field['options_from'][0]), $field['options_from'][1])
                    : Rule::in(array_column($field['options'] ?? [], 'value'))],
                default => ['string', 'max:'.($field['max'] ?? 100)],
            }];

            if ($field['unique'] ?? false) {
                $unique = Rule::unique($table, $column)->ignore($ignore);

                if (is_string($field['unique'])) {
                    $unique->where($field['unique'], $input[$field['unique']] ?? null);
                }

                $list[] = $unique;
            }

            $rules[$column] = [...$list, ...($field['rules'] ?? [])];
        }

        return $rules;
    }

    /**
     * Case-insensitive search across the text columns.
     *
     * @param  Builder<Model>  $query
     */
    public function search(Builder $query, ?string $term): void
    {
        if (blank($term)) {
            return;
        }

        $like = '%'.addcslashes($term, '%_\\').'%';
        $columns = array_column(array_filter($this->rawFields(), fn (array $f): bool => $f['type'] === 'text'), 'name');

        $query->where(function (Builder $q) use ($columns, $like): void {
            foreach ($columns as $column) {
                $q->orWhere($column, 'like', $like);
            }
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function toItem(Model $record): array
    {
        $item = ['id' => $record->getKey()];

        foreach ($this->rawFields() as $field) {
            $value = $record->{$field['name']};
            $item[$field['name']] = $value instanceof \BackedEnum ? $value->value : $value;
        }

        return $item;
    }

    /**
     * @param  class-string<Model>  $model
     */
    private function tableOf(string $model): string
    {
        return (new $model)->getTable();
    }
}
