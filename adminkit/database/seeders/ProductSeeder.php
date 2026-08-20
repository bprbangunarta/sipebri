<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

/** Produk kredit — mengikuti data core banking (CBS). */
class ProductSeeder extends Seeder
{
    /** [kode CBS, alias, nama] */
    private const PRODUCTS = [
        ['01', 'KRU', 'KREDIT UMUM'],
        ['02', 'KUP', 'KREDIT PEGAWAI'],
        ['03', 'KRM', 'KREDIT MOTOR'],
        ['04', 'PRK', 'KREDIT REKENING KORAN'],
        ['05', 'KTO', 'KREDIT TAKE OVER'],
        ['06', 'KBT', 'KREDIT BUDIDAYA PERTANIAN'],
        ['07', 'KPS', 'KREDIT PEGAWAI SWASTA'],
        ['08', 'KKO', 'KREDIT KENDARAAN OPERASIONAL'],
        ['09', 'KIH', 'KREDIT IBADAH HAJI'],
        ['10', 'KPJ', 'KREDIT PEGAWAI SWASTA NON MOU'],
        ['11', 'KRS', 'KREDIT RESEPSI'],
        ['12', 'KPN', 'KREDIT PEGAWAI NEGERI'],
        ['13', 'KIU', 'KREDIT IBADAH UMROH'],
        ['14', 'KTA', 'KREDIT TANPA AGUNAN'],
        ['15', 'KPMI', 'KREDIT PEKERJA MIGRAN INDONESIA'],
        ['16', 'KPP', 'KREDIT PENSIUN PN (CHANNELING)'],
        ['17', 'KRISPI', 'KREDIT PASANG MINGGUAN'],
    ];

    public function run(): void
    {
        foreach (self::PRODUCTS as [$code, $alias, $name]) {
            Product::updateOrCreate(['code' => $code], ['alias' => $alias, 'name' => $name]);
        }
    }
}
