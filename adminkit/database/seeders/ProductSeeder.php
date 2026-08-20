<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

/** Produk kredit — mengikuti data core banking (CBS) dan status terakhir di aplikasi. */
class ProductSeeder extends Seeder
{
    /** [kode CBS, alias, nama, aktif] */
    private const PRODUCTS = [
        ['01', 'KRU', 'KREDIT UMUM', true],
        ['02', 'KUP', 'KREDIT PEGAWAI', true],
        ['03', 'KRM', 'KREDIT MOTOR', false],
        ['04', 'PRK', 'KREDIT REKENING KORAN', false],
        ['05', 'KTO', 'KREDIT TAKE OVER', true],
        ['06', 'KBT', 'KREDIT BUDIDAYA PERTANIAN', true],
        ['07', 'KPS', 'KREDIT PEGAWAI SWASTA', true],
        ['08', 'KKO', 'KREDIT KENDARAAN OPERASIONAL', true],
        ['09', 'KIH', 'KREDIT IBADAH HAJI', true],
        ['10', 'KPJ', 'KREDIT PEGAWAI SWASTA NON MOU', true],
        ['11', 'KRS', 'KREDIT RESEPSI', true],
        ['12', 'KPN', 'KREDIT PEGAWAI NEGERI', false],
        ['13', 'KIU', 'KREDIT IBADAH UMROH', true],
        ['14', 'KTA', 'KREDIT TANPA AGUNAN', true],
        ['15', 'KPMI', 'KREDIT PEKERJA MIGRAN INDONESIA', true],
        ['16', 'KPP', 'KREDIT PENSIUN PN (CHANNELING)', true],
        ['17', 'KRISPI', 'KREDIT PASAR MINGGUAN', true],
    ];

    public function run(): void
    {
        foreach (self::PRODUCTS as [$code, $alias, $name, $active]) {
            Product::updateOrCreate(
                ['code' => $code],
                ['alias' => $alias, 'name' => $name, 'is_active' => (int) $active],
            );
        }
    }
}
