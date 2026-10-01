<?php

namespace App\Support\CreditAnalysis;

/**
 * How much the applicant can repay: the biggest loan whose monthly installment stays within the share of the monthly
 * balance that the product allows (the RC threshold). The committee sees the proposal as a percentage of that loan.
 * The formulas are those of the approval screen used before this system.
 */
final class RepaymentCapacity
{
    /** Used when the product has no RC threshold. */
    public const FALLBACK_THRESHOLD = 70.0;

    /**
     * @param  int  $capacity  what is left each month after household costs and obligations (never below 0)
     * @param  float  $threshold  percentage of it that may go to the installment
     * @param  float  $rate  yearly interest in percent
     * @param  string  $method  the interest method; annuity and effective methods amortise, the rest are flat
     */
    public static function maxAmount(int $capacity, float $threshold, float $rate, int $tenor, string $method): int
    {
        $installment = $capacity * ($threshold / 100);

        if ($installment <= 0 || $tenor <= 0) {
            return 0;
        }

        $monthly = $rate / 100 / 12;

        if ($monthly <= 0) {
            return (int) round($installment * $tenor);
        }

        $effective = str_contains(strtoupper($method), 'ANUITAS') || str_contains(strtoupper($method), 'EFEKTIF');

        return (int) round($effective
            ? $installment * (1 - (1 + $monthly) ** -$tenor) / $monthly
            : $installment / (1 / $tenor + $monthly));
    }

    /** The loan as a percentage of the biggest the applicant can repay. */
    public static function ratio(int $amount, int $maxAmount): float
    {
        return $maxAmount > 0 ? round($amount / $maxAmount * 100, 2) : 0.0;
    }
}
