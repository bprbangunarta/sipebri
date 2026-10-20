<?php

namespace App\Support\Appraisal;

use Illuminate\Validation\Rule;

/**
 * What an answer may be, by the type of its item. The server decides; the screen picks controls that cannot type more.
 */
final class AppraisalRules
{
    public const NOTE_MAX = 500;

    /**
     * @param  array<string, mixed>  $item
     * @return list<mixed>
     */
    public static function for(array $item): array
    {
        return ['nullable', ...match ($item['type']) {
            'textarea' => ['string', 'max:2000'],
            'integer' => ['integer', 'min:0', 'max:99999999'],
            'money' => ['integer', 'min:0', 'max:999999999999'],
            'percent' => ['decimal:0,2', 'min:0', 'max:100'],
            'adjustment' => ['decimal:0,2', 'min:-100', 'max:100'],
            'date' => ['date_format:Y-m-d'],
            'choice', 'yesno', 'rating', 'risk' => [Rule::in(array_keys(Fields::optionsOf($item)))],
            default => ['string', 'max:255'],
        }];
    }

    /**
     * Items that are answered: not the ones the system works out and not the places for photos.
     *
     * @param  array<string, mixed>  $item
     */
    public static function answerable(array $item): bool
    {
        return ! in_array($item['type'], ['computed', 'photo'], true);
    }
}
