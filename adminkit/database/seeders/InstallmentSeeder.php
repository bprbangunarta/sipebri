<?php

namespace Database\Seeders;

use App\Models\Installment;
use Illuminate\Database\Seeder;

/** Sistem cicilan (pola angsuran) — mengikuti data core banking (CBS). */
class InstallmentSeeder extends Seeder
{
    /** [kode, nama, kelipatan jangka waktu dalam bulan] */
    private const INSTALLMENTS = [
        ['1', 'HARIAN', 1],
        ['2', 'MINGGUAN', 1],
        ['3', 'BULANAN', 1],
        ['4', 'TRIWULANAN', 3],
        ['5', 'SEMESTERAN', 6],
        ['6', 'TAHUNAN', 12],
        ['7', 'MUSIMAN', 6],
        ['8', 'NON ANGSURAN', 0],
    ];

    public function run(): void
    {
        foreach (self::INSTALLMENTS as [$code, $name, $period]) {
            Installment::updateOrCreate(['code' => $code], ['name' => $name, 'period_months' => $period]);
        }
    }
}
