<?php

namespace App\Support\Appraisal;

/**
 * Writes the items of a template. An item is an array with a key, a label, and a type:
 * text, textarea, integer, money, percent, adjustment (a percent that may be negative), date, choice, yesno, rating
 * (good, fair, poor), risk (low, medium, high), or computed (shown, never entered), or photo (a place for pictures, in a photos block).
 */
final class Fields
{
    public const TYPES = ['text', 'textarea', 'integer', 'money', 'percent', 'adjustment', 'date', 'choice', 'yesno', 'rating', 'risk', 'computed', 'photo'];

    public const OPTIONS = [
        'yesno' => ['yes' => 'Ya', 'no' => 'Tidak'],
        'rating' => ['good' => 'Baik', 'fair' => 'Cukup', 'poor' => 'Kurang'],
        'risk' => ['low' => 'Rendah', 'medium' => 'Sedang', 'high' => 'Tinggi'],
    ];

    /**
     * @param  array<string, mixed>  $extra  `options` (a choice), `unit`, `hint`, `format` (a computed item), `readonly`
     * @return array<string, mixed>
     */
    public static function item(string $key, string $label, string $type = 'text', array $extra = []): array
    {
        return ['key' => $key, 'label' => $label, 'type' => $type, ...$extra];
    }

    /**
     * @param  array<string, string>  $options  value => label
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    public static function choice(string $key, string $label, array $options, array $extra = []): array
    {
        return self::item($key, $label, 'choice', ['options' => $options, ...$extra]);
    }

    /**
     * @param  array<string, string>  $labels  item key => label
     * @return list<array<string, mixed>>
     */
    public static function many(string $type, array $labels, string $prefix = ''): array
    {
        $items = [];

        foreach ($labels as $key => $label) {
            $items[] = self::item($prefix.$key, $label, $type);
        }

        return $items;
    }

    /**
     * The options of an item: the fixed scale of a yes/no, rating, or risk item, or the ones a choice lists.
     *
     * @param  array<string, mixed>  $item
     * @return array<string, string>
     */
    public static function optionsOf(array $item): array
    {
        return $item['options'] ?? self::OPTIONS[$item['type']] ?? [];
    }

    /**
     * @param  array<string, mixed>  $block
     * @return list<array<string, mixed>>
     */
    public static function itemsOf(string $section, array $block): array
    {
        if (($block['layout'] ?? '') !== 'grid') {
            return array_values($block['items'] ?? []);
        }

        $items = [];

        foreach ($block['rows'] as $row) {
            foreach ($block['columns'] as $column) {
                $type = $row['type'] ?? $column['type'] ?? 'text';
                $computed = ($column['computed'] ?? false) || ($row['computed'] ?? false);
                $items[] = [
                    'key' => "{$section}.{$row['key']}.{$column['key']}", 'label' => "{$row['label']}: {$column['label']}",
                    'type' => $computed ? 'computed' : $type, 'options' => $row['options'] ?? $column['options'] ?? null,
                ];
            }
        }

        return $items;
    }
}
