<?php

namespace Database\Seeders;

use App\Models\CollateralSimulation;
use Illuminate\Database\Seeder;

/** Dua contoh agunan dari form CBS yang dilampirkan user. */
class CollateralSimulationSeeder extends Seeder
{
    private const SAMPLES = [
        [
            'collateral_id' => '0141990',
            'collateral_type_code' => '14',
            'binding_type_code' => null,
            'ownership' => 'LAINNYA',
            'document_number' => null,
            'description' => 'KARTU JAMSOSTEK ATAS NAMA YOYOH TOHAROH NO 22042925218 ALAMA',
            'owner_name' => 'YOYOH TOHAROH',
            'owner_address' => 'DUSUN KOSEDAN SELATAN RT/RW 10/02 TANJUN',
            'region_code' => '0121',
            'region_label' => '0121 · Kab. Subang',
            'value_guarantee' => 17295845,
            'value_fair' => 17295845,
            'value_appraisal' => 17295845,
            'appraised_at' => '2024-09-26',
            'condition_code' => '9',
            'insured' => 'T',
            'ppap_code' => '1',
            'insurance_start_date' => '2024-09-27',
        ],
        [
            'collateral_id' => '01.3.001419',
            'collateral_type_code' => '05',
            'binding_type_code' => '01',
            'ownership' => null,
            'document_number' => 'No. 1774',
            'description' => 'SERTIFIKA TANAH DAN BANGUNAN NO 1774 LUAS 72 M2',
            'owner_name' => 'KANA',
            'owner_address' => 'PAMANUKAN PAMANUKAN SUBANG JAWA BARAT',
            'region_code' => '0121',
            'region_label' => '0121 · Kab. Subang',
            'value_guarantee' => 300000000,
            'value_fair' => 300000000,
            'value_njop' => 300000000,
            'value_appraisal' => 73740000,
            'appraised_at' => '2026-05-12',
            'appraiser_name' => 'NAUFAL',
            'condition_code' => '9',
            'insured' => 'T',
            'ppap_code' => '1',
            'insurance_start_date' => '2026-05-12',
        ],
    ];

    public function run(): void
    {
        foreach (self::SAMPLES as $sample) {
            CollateralSimulation::updateOrCreate(
                ['collateral_id' => $sample['collateral_id']],
                $sample,
            );
        }
    }
}
