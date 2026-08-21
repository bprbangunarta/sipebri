<?php

namespace Database\Seeders;

use App\Models\CollateralSimulation;
use App\Models\CommitteePath;
use App\Models\Installment;
use App\Models\Institution;
use App\Models\LoanApplication;
use App\Models\LoanSchedule;
use App\Models\LoanSurvey;
use App\Models\LoanSurveyPhoto;
use App\Models\Method;
use App\Models\Office;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Berkas pengajuan contoh untuk mencoba alur kredit sampai analisa.
 * Idempoten: berkas yang kodenya sudah ada TIDAK ditimpa.
 * Kunci relasi memakai kode alami (alias produk, kode kantor, username) agar
 * tetap benar meski id di basis data berbeda.
 */
class LoanApplicationSeeder extends Seeder
{
    /** Agunan yang dipakai berkas di bawah (kunci: nomor dokumen). */
    private const COLLATERALS = [
        [
            'document_number' => '23026932550',
            'collateral_type_code' => '14',
            'description' => 'KARTU DAN SALDO JAMSOSTEK ATAS NAMA WIDYA PUSPITA NO 23026932550',
            'owner_name' => 'WIDYA PUSPITA',
            'owner_address' => 'KAMPUNG CICARIU RT/RW 24/04 BUNIHAYU JALANCAGAK SUBANG',
        ],
        [
            'document_number' => '14038023132',
            'collateral_type_code' => '14',
            'description' => 'KARTU JAMSOSTEK ATAS NAMA TUTI HELAWATI NO 14038023132',
            'owner_name' => 'TUTI HELAWATI',
            'owner_address' => 'KAMPUNG RANCABOGO RT/RW 22/06 SUKAMULYA PAGADEN SUBANG',
        ],
        [
            'document_number' => '01288',
            'collateral_type_code' => '05',
            'binding_type_code' => '01',
            'description' => 'SERTIFIKAT TANAH NO 01288, LUAS 5.711 M2, ATAS NAMA WASPEN BT ANWAR',
            'owner_name' => 'WASPEN BT ANWAR',
            'owner_address' => 'ARJASARI PATROL INDRAMAYU JAWA BARAT',
        ],
    ];

    private const APPLICATIONS = [
        [
            'application_code' => '00700003',
            'status' => 'SURVEY',
            'cif_number' => '01.1.038586',
            'nik' => '3213070701980004',
            'full_name' => 'ZULFADLI RIZAL',
            'office' => 'SBG',
            'product' => 'KTA',
            'institution' => '006',
            'method' => '10',
            'installment' => '3',
            'requested_amount' => 5_000_000,
            'requested_tenor' => 10,
            'interest_rate' => 60,
            'usage_type' => 'KONSUMTIF',
            'marketing' => 'YOLANDA ISMI SOPANDI',
            'supervisor' => '286010620',
            'surveyor' => '287010620',
        ],
        [
            'application_code' => '00700004',
            'status' => 'SURVEY',
            'cif_number' => '01.1.051930',
            'nik' => '3213044411940014',
            'full_name' => 'WIDYA PUSPITA',
            'office' => 'SBG',
            'product' => 'KPS',
            'institution' => '002',
            'method' => '30',
            'installment' => '3',
            'requested_amount' => 30_000_000,
            'requested_tenor' => 36,
            'interest_rate' => 32,
            'usage_type' => 'KONSUMTIF',
            'marketing' => 'ZULFADLI RIZAL',
            'supervisor' => '286010620',
            'surveyor' => '350010923',
            'collaterals' => ['23026932550'],
            'schedule' => ['note' => 'BAWA BERKAS AGUNAN'],
            'survey' => [
                'note' => 'LOKASI SESUAI, USAHA AKTIF',
                'latitude' => -6.5712345,
                'longitude' => 107.7601234,
                'photo' => 's3:survei/00700004/pgzN1WGCzCwQfKFmmMvfQz0FllgIKl3l2KTgs1eO.jpg',
            ],
        ],
        [
            'application_code' => '00700005',
            'status' => 'DRAFT',
            'cif_number' => '01.1.049939',
            'nik' => '3213034401970010',
            'full_name' => 'HANI TANIA',
            'office' => 'SBG',
            'product' => 'KTA',
            'method' => '10',
            'installment' => '3',
            'requested_amount' => 5_000_000,
            'requested_tenor' => 10,
            'interest_rate' => 60,
            'usage_type' => 'KONSUMTIF',
            'supervisor' => '286010620',
        ],
        [
            'application_code' => '00700006',
            'status' => 'DRAFT',
            'cif_number' => '01.1.035387',
            'nik' => '3213075011810002',
            'full_name' => 'TUTI HELAWATI',
            'office' => 'SBG',
            'product' => 'KPS',
            'method' => '30',
            'installment' => '3',
            'requested_amount' => 70_000_000,
            'requested_tenor' => 36,
            'interest_rate' => 32,
            'usage_type' => 'KONSUMTIF',
            'supervisor' => '286010620',
            'collaterals' => ['14038023132'],
        ],
        [
            'application_code' => '00700007',
            'status' => 'DRAFT',
            'cif_number' => '01.1.048500',
            'nik' => '3212225707820002',
            'full_name' => 'WASPEN BINTI ANWAR',
            'office' => 'SBG',
            'product' => 'KBT',
            'condition' => 'PERPADIAN',
            'method' => '22',
            'installment' => '7',
            'requested_amount' => 45_000_000,
            'requested_tenor' => 12,
            'interest_rate' => 15,
            'usage_type' => 'MODAL USAHA',
            'supervisor' => '286010620',
            'collaterals' => ['01288'],
        ],
    ];

    public function run(): void
    {
        $collaterals = $this->collaterals();

        foreach (self::APPLICATIONS as $row) {
            if (LoanApplication::withTrashed()->where('application_code', $row['application_code'])->exists()) {
                continue;
            }

            $product = Product::where('alias', $row['product'])->first();
            $surveyor = isset($row['surveyor']) ? User::where('username', $row['surveyor'])->first() : null;

            $application = LoanApplication::create([
                'application_code' => $row['application_code'],
                'application_date' => now()->toDateString(),
                'status' => $row['status'],
                'cif_number' => $row['cif_number'],
                'nik' => $row['nik'],
                'full_name' => $row['full_name'],
                'office_id' => Office::where('alias', $row['office'])->value('id'),
                'product_id' => $product?->id,
                'institution_id' => isset($row['institution'])
                    ? Institution::where('code', $row['institution'])->value('id')
                    : null,
                'method_id' => Method::where('code', $row['method'])->value('id'),
                'installment_id' => Installment::where('code', $row['installment'])->value('id'),
                'committee_path_id' => CommitteePath::where('product_id', $product?->id)
                    ->where('condition', $row['condition'] ?? null)
                    ->value('id'),
                'requested_amount' => $row['requested_amount'],
                'requested_tenor' => $row['requested_tenor'],
                'interest_rate' => $row['interest_rate'],
                'usage_type' => $row['usage_type'],
                'marketing' => $row['marketing'] ?? null,
                'supervisor_id' => User::where('username', $row['supervisor'])->value('id'),
                'surveyor_id' => $surveyor?->id,
                'survey_date' => $surveyor ? now()->toDateString() : null,
                'created_by' => 'IT Support',
            ]);

            $ids = collect($row['collaterals'] ?? [])->map(fn ($doc) => $collaterals[$doc])->all();

            if ($ids) {
                $application->collaterals()->sync($ids);
            }

            if (isset($row['schedule'])) {
                LoanSchedule::create([
                    'loan_application_id' => $application->id,
                    'sequence' => 1,
                    'action' => 'JADWAL',
                    'survey_date' => now()->toDateString(),
                    'surveyor_id' => $surveyor->id,
                    'surveyor_name' => $surveyor->name,
                    'note' => $row['schedule']['note'] ?? null,
                    'created_by' => 'Dede Doni',
                ]);
            }

            if (isset($row['survey'])) {
                $survey = LoanSurvey::create([
                    'loan_application_id' => $application->id,
                    'sequence' => 1,
                    'surveyor_id' => $surveyor->id,
                    'surveyor_name' => $surveyor->name,
                    'survey_date' => now()->toDateString(),
                    'note' => $row['survey']['note'],
                    'latitude' => $row['survey']['latitude'],
                    'longitude' => $row['survey']['longitude'],
                    'created_by' => $surveyor->name,
                ]);

                LoanSurveyPhoto::create([
                    'loan_application_id' => $application->id,
                    'loan_survey_id' => $survey->id,
                    'path' => $row['survey']['photo'],
                    'latitude' => $row['survey']['latitude'],
                    'longitude' => $row['survey']['longitude'],
                    'source' => 'KAMERA',
                    'created_by' => $surveyor->name,
                ]);
            }
        }
    }

    /** @return array<string,int> nomor dokumen → id agunan */
    private function collaterals(): array
    {
        $ids = [];

        foreach (self::COLLATERALS as $row) {
            $collateral = CollateralSimulation::firstOrCreate(
                ['document_number' => $row['document_number']],
                [
                    'collateral_type_code' => $row['collateral_type_code'],
                    'binding_type_code' => $row['binding_type_code'] ?? null,
                    'description' => $row['description'],
                    'owner_name' => $row['owner_name'],
                    'owner_address' => $row['owner_address'],
                    'region_code' => '0121',
                    'region_label' => '0121 : Kab. Subang',
                    'insurance_code' => 'T',
                    'ppap_code' => '1',
                    'created_by' => 'IT Support',
                ],
            );

            $ids[$row['document_number']] = $collateral->id;
        }

        return $ids;
    }
}
