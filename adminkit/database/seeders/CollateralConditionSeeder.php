<?php

namespace Database\Seeders;

use App\Models\CollateralCondition;
use Illuminate\Database\Seeder;

/** Kondisi agunan — mengikuti data core banking (CBS). */
class CollateralConditionSeeder extends Seeder
{
    private const CONDITIONS = [
        ['1', 'TELAH DIGUNAKAN SEBAGAI FASUM/FASOS'],
        ['2', 'DALAM SENGKETA'],
        ['3', 'DISITA NEGARA'],
        ['4', 'TDK DIKETAHUI KEBERADAANNYA'],
        ['5', 'TDK MEMILIKI NILAI EKONOMIS LAGI'],
        ['6', 'TDK DAPAT DIEKSEKUSI'],
        ['9', 'TIDAK ADA MASALAH'],
    ];

    public function run(): void
    {
        foreach (self::CONDITIONS as [$code, $name]) {
            CollateralCondition::updateOrCreate(['code' => $code], ['name' => $name]);
        }
    }
}
