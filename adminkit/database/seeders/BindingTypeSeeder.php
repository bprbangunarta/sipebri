<?php

namespace Database\Seeders;

use App\Models\BindingType;
use Illuminate\Database\Seeder;

/** Jenis pengikatan agunan — mengikuti data core banking (CBS). */
class BindingTypeSeeder extends Seeder
{
    private const TYPES = [
        ['01', 'APHT : HAK TANGGUNGAN'],
        ['02', 'GADAI'],
        ['03', 'FEO : FIDUCIARE EIGENDOM OVERDRACHT'],
        ['04', 'SKMHT : SURAT KUASA MEMBEBANKAN HAK TANGGUNGAN'],
        ['05', 'CESSIE'],
        ['06', 'BELUM DIIKAT'],
        ['99', 'LAINNYA'],
    ];

    public function run(): void
    {
        foreach (self::TYPES as [$code, $name]) {
            BindingType::updateOrCreate(['code' => $code], ['name' => $name]);
        }
    }
}
