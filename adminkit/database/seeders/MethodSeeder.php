<?php

namespace Database\Seeders;

use App\Models\Method;
use Illuminate\Database\Seeder;

/** Sistem bunga (metode perhitungan) — mengikuti data core banking (CBS). */
class MethodSeeder extends Seeder
{
    private const METHODS = [
        ['10', 'FLATE'],
        ['14', 'FLATE GP DISTRIBUSI'],
        ['16', 'FLATE MUSIMAN'],
        ['20', 'EFEKTIF HARIAN (RC)'],
        ['21', 'EFEKTIF HARIAN (NON ANGSUR)'],
        ['22', 'EFEKTIF BULANAN'],
        ['24', 'EFEKTIF NON ANGSUR'],
        ['30', 'ANUITAS'],
        ['40', 'KONVERSI'],
        ['41', 'KONVERSI CARI BUNGA'],
    ];

    public function run(): void
    {
        foreach (self::METHODS as [$code, $name]) {
            Method::updateOrCreate(['code' => $code], ['name' => $name]);
        }
    }
}
