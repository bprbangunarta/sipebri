<?php

namespace Database\Seeders;

use App\Models\OwnershipStatus;
use Illuminate\Database\Seeder;

/**
 * Status/bukti kepemilikan per jenis agunan (mengikuti CBS).
 * Baru jenis 05 (TANAH/BNGN-SERTIFIKAT DGN HT) yang dipastikan user.
 */
class OwnershipStatusSeeder extends Seeder
{
    private const STATUSES = [
        '05' => [
            'PEKARANGAN', 'SAWAH', 'KEBUN/HUTAN', 'RUMAHTINGGAL', 'RUMAHSUSUN',
            'RUKO/RUKAN', 'HOTEL', 'GUDANG', 'GEDUNG', 'BANGUNAN', 'LAINNYA',
        ],
    ];

    public function run(): void
    {
        foreach (self::STATUSES as $type => $names) {
            foreach ($names as $i => $name) {
                OwnershipStatus::updateOrCreate(
                    ['collateral_type_code' => $type, 'code' => str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)],
                    ['name' => $name],
                );
            }
        }
    }
}
