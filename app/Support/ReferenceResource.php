<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

/**
 * Typed view over one entry of config/references.php.
 */
class ReferenceResource
{
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
