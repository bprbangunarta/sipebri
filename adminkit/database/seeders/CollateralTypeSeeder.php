<?php

namespace Database\Seeders;

use App\Models\CollateralType;
use Illuminate\Database\Seeder;

/** Jenis agunan — mengikuti data core banking (CBS). */
class CollateralTypeSeeder extends Seeder
{
    private const TYPES = [
        ['01', 'SBI/SPN/ON/OR'],
        ['02', 'TABUNGAN / DEPOSITO'],
        ['03', 'LOGAM MULIA'],
        ['04', 'PERHIASAN EMAS'],
        ['05', 'TANAH/BNGN-SERTIFIKAT DGN HT'],
        ['06', 'TANAH/BNGN-SERTIFIKAT NON HT'],
        ['07', 'TANAH/BNGN-SURAT ADAT + SPPT'],
        ['08', 'TEMPAT USAHA'],
        ['09', 'RESI GUDANG'],
        ['10', 'KENDARAAN / KAPAL - FIDUCIA'],
        ['11', 'KENDARAAN / KAPAL - NOTARIEL'],
        ['12', 'KENDARAAN / KAPAL - LAINNYA'],
        ['13', 'BAGIAN DANA YG DIJAMIN BUMN/BUMD'],
        ['14', 'LAINNYA : SK/IJAZAH'],
        ['15', 'LAINNYA : SAHAM'],
        ['16', 'LAINNYA : REKSADANA'],
        ['17', 'LAINNYA : PERSEDIAAN'],
        ['18', 'LAINNYA : LAIN-LAIN'],
        ['99', 'TANPA AGUNAN'],
    ];

    public function run(): void
    {
        foreach (self::TYPES as [$code, $name]) {
            CollateralType::updateOrCreate(['code' => $code], ['name' => $name]);
        }
    }
}
