<?php

namespace App\Support\Appraisal;

/**
 * Reads the answers of an appraisal as numbers and does the arithmetic the forms ask for. Money is whole rupiah and
 * percentages keep two decimals.
 */
final class Amounts
{
    public static function whole(?string $value): ?int
    {
        return $value === null || $value === '' || ! is_numeric($value) ? null : (int) round((float) $value);
    }

    public static function decimal(?string $value): ?float
    {
        return $value === null || $value === '' || ! is_numeric($value) ? null : (float) $value;
    }

    /** The share of an amount, for example 85% of a value, in whole rupiah. */
    public static function share(int $amount, float $percent): int
    {
        return (int) round($amount * $percent / 100);
    }

    /** One amount as a percentage of another, to two decimals; null when there is nothing to divide by. */
    public static function ratio(int $part, int $whole): ?float
    {
        return $whole > 0 ? round($part * 100 / $whole, 2) : null;
    }

    /** What is left of a value after taking off a percentage of it. */
    public static function after(int $amount, float $percentOff): int
    {
        return $amount - self::share($amount, $percentOff);
    }
}
