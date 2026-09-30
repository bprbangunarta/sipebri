<?php

namespace App\Support;

use App\Models\Setting;

/**
 * BMPK (Batas Maksimum Pemberian Kredit): the most the bank may lend in one loan. Kept deliberately simple: one amount that a
 * Super Admin can change when the regulation changes (the full BMPK calculation is not modelled). The starting value comes from
 * the seeder; every change is recorded in the audit trail with the value before and after.
 */
class LendingLimit
{
    public const KEY = 'bmpk';

    /** Starting value: Rp 2 billion, the amount of the previous rule. */
    public const DEFAULT_BMPK = 2_000_000_000;

    /** The limit in rupiah, or null when none is set (then only the product limits apply). */
    public static function bmpk(): ?int
    {
        $value = Setting::query()->where('key', self::KEY)->value('value');

        return is_numeric($value) && (int) $value > 0 ? (int) $value : null;
    }

    public static function setBmpk(int $amount): void
    {
        Setting::query()->updateOrCreate(['key' => self::KEY], ['value' => (string) $amount]);
    }

    public static function format(int|float|string|null $amount): string
    {
        return 'Rp'.number_format((float) $amount, 0, ',', '.');
    }
}
