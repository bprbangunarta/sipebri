<?php

namespace Database\Seeders;

use App\Models\Installment;
use Illuminate\Database\Seeder;

/** Sistem cicilan (pola angsuran) — mengikuti data core banking (CBS). */
class InstallmentSeeder extends Seeder
{
    private const INSTALLMENTS = [
        ['1', 'HARIAN'],
        ['2', 'MINGGUAN'],
        ['3', 'BULANAN'],
        ['4', 'TRIWULANAN'],
        ['5', 'SEMESTERAN'],
        ['6', 'TAHUNAN'],
        ['7', 'MUSIMAN'],
        ['8', 'NON ANGSURAN'],
    ];

    public function run(): void
    {
        foreach (self::INSTALLMENTS as [$code, $name]) {
            Installment::updateOrCreate(['code' => $code], ['name' => $name]);
        }
    }
}
