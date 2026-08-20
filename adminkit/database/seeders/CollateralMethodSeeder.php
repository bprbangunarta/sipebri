<?php

namespace Database\Seeders;

use App\Models\CollateralMethod;
use Illuminate\Database\Seeder;

/** Metode hitung nilai agunan — mengikuti data core banking (CBS). */
class CollateralMethodSeeder extends Seeder
{
    private const METHODS = [
        ['0', 'DEFAULT'],
        ['1', 'NETTO'],
        ['2', 'GROSS'],
        ['3', 'FULL'],
    ];

    public function run(): void
    {
        foreach (self::METHODS as [$code, $name]) {
            CollateralMethod::updateOrCreate(['code' => $code], ['name' => $name]);
        }
    }
}
