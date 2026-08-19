<?php

namespace Database\Seeders;

use App\Models\Institution;
use Illuminate\Database\Seeder;

/**
 * Data instansi (perusahaan mitra) — mengikuti data core banking (CBS).
 * Belum seluruh instansi dimasukkan; tambahkan lewat modul Data Instansi.
 */
class InstitutionSeeder extends Seeder
{
    /** [kode, nama] */
    private const INSTITUTIONS = [
        ['001', 'KLINIK HAPPY HEALTY'],
        ['002', 'PT.KWANGLIMYHI'],
        ['003', 'PT.KWANGLIMYHI'],
        ['004', 'PT.KWANGLIMYHI'],
        ['005', 'PT.KWANGLIMYHI'],
        ['006', 'PT.TAEKWANG'],
        ['007', 'PT.SHEBAINDAH'],
        ['008', 'PT.PANPACIFICNESIA'],
        ['009', 'PT.C-SITETEXPIA'],
        ['10', 'PT.ABB'],
    ];

    public function run(): void
    {
        foreach (self::INSTITUTIONS as [$code, $name]) {
            Institution::updateOrCreate(['code' => $code], ['name' => $name]);
        }
    }
}
