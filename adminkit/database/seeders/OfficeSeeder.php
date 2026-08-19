<?php

namespace Database\Seeders;

use App\Models\Office;
use Illuminate\Database\Seeder;

/** Data kantor — mengikuti data core banking (CBS). */
class OfficeSeeder extends Seeder
{
    /** [kode, alias, nama] */
    private const OFFICES = [
        ['00', 'PMK', 'Pamanukan'],
        ['01', 'CGK', 'Jalancagak'],
        ['02', 'SBG', 'Subang'],
        ['03', 'SKM', 'Sukamandi'],
        ['04', 'PGD', 'Pagaden'],
        ['05', 'KJT', 'Kalijati'],
        ['06', 'PSK', 'Pusakajaya'],
    ];

    public function run(): void
    {
        foreach (self::OFFICES as [$code, $alias, $name]) {
            Office::updateOrCreate(['code' => $code], ['alias' => $alias, 'name' => $name]);
        }
    }
}
