<?php

namespace Database\Seeders;

use App\Audit\Audit;
use App\Enums\RoleName;
use App\Models\Collateral;
use App\Models\User;
use App\Support\Appraisal\AppraisalFinalisation;
use App\Support\Appraisal\AppraisalSaver;
use App\Support\Appraisal\AppraisalStarter;
use App\Support\Appraisal\AppraisalTemplate;
use App\Support\Appraisal\AppraisalTemplates;
use Database\Seeders\Examples\LandExamples;
use Database\Seeders\Examples\VehicleExamples;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * Sample collaterals shaped like the screens of the dummy core banking (two kinds of land and building, a car, a motorcycle, and two
 * supporting documents), and four appraisals filled in through every section of their form, to try the screens with and to show how a
 * form is filled. Every appraisal says it is a sample. Idempotent, and never runs in production: a collateral that exists is left as it
 * is, and one that already has an appraisal gets no more.
 */
class CollateralExampleSeeder extends Seeder
{
    private const SUBANG = ['region_code' => '0121', 'region_label' => 'SUBANG, KAB.', 'province' => 'JAWA BARAT'];

    private const HAMLET = 'DUSUN CIMANGGU RT 09 RW 03, PAMANUKAN HILIR, PAMANUKAN,';

    public function run(): void
    {
        if (app()->isProduction()) {
            return;
        }

        Audit::withoutAuditing(function (): void {
            foreach ($this->collaterals() as $row) {
                $collateral = Collateral::query()->firstOrCreate(['cbs_id' => $row['collateral']['cbs_id']], $row['collateral']);

                if (isset($row['answers']) && $collateral->appraisals()->doesntExist()) {
                    $this->appraise($collateral, $row['answers'], $row['purpose'] ?? 'new');
                }
            }
        });
    }

    /**
     * What the screens show, one entry per collateral; the legacy figures stay in the collateral's own value columns.
     *
     * @return list<array{collateral: array<string, mixed>, answers?: array<string, string|array{0: string, 1: string}>, purpose?: string}>
     */
    private function collaterals(): array
    {
        $land = ['condition_code' => '9', 'insurance_code' => 'T', 'ppap_code' => '1', 'adjustment_value' => 0, 'independent_value' => 0, ...self::SUBANG];
        $vehicle = ['collateral_type_code' => '10', 'binding_type_code' => '03', 'condition_code' => '9', 'ppap_code' => '1', 'adjustment_value' => 0, 'independent_value' => 0, ...self::SUBANG];
        $document = ['collateral_type_code' => '14', 'binding_type_code' => '99', 'condition_code' => '9', 'insurance_code' => 'T', 'insurance_date' => '2026-08-07', 'ppap_code' => '1', ...self::SUBANG];

        return [
            ['collateral' => [...$land, 'cbs_id' => '01.3.002194', 'credit_account' => '01.301.028020', 'collateral_type_code' => '05', 'binding_type_code' => '01', 'document_number' => 'No. 321505001201401020',
                'description' => 'LETTER C/GIRIK TANAH DAN BANGUNAN LUAS 197 M2', 'owner_name' => 'RIKA', 'owner_address' => 'JALAN KAPTEN HANAFIAH RAWABADAK RT/RW 101/28 KARANGANYAR SUBANG',
                'guarantee_value' => 437_500_000, 'fair_value' => 437_500_000, 'njop_value' => 437_500_000, 'appraisal_value' => 129_826_667, 'appraised_at' => '2026-10-07', 'appraiser_name' => 'PAJAR',
                'insurance_date' => '2026-10-07', 'certificate_land_area' => 197, 'right_kind' => 'OTHER'], 'answers' => LandExamples::customaryPapers()],
            ['collateral' => [...$vehicle, 'cbs_id' => '01.3.002195', 'credit_account' => '01.301.028020', 'document_number' => 'No. J-05868974',
                'description' => 'MOBIL: BPKB R4, MHYKZE81SCJ134451, K14BT1034570, D 1612 AB', 'owner_name' => 'NURHALIMAH', 'owner_address' => 'JALAN KAPTEN HANAFIAH RAWABADAK RT/RW 100/28 KARANGANYAR SUBANG',
                'guarantee_value' => 90_000_000, 'fair_value' => 90_000_000, 'njop_value' => 0, 'appraisal_value' => 90_000_000, 'appraised_at' => '2026-10-07', 'appraiser_name' => 'PAJAR',
                'insurance_code' => 'Y', 'insurance_date' => '2026-10-07', 'vehicle_kind' => 'wheel4', 'vehicle_brand' => 'SUZUKI', 'vehicle_model' => 'MOBIL', 'manufacture_year' => 2012,
                'plate_number' => 'D 1612 AB', 'chassis_number' => 'MHYKZE81SCJ134451', 'engine_number' => 'K14BT1034570', 'bpkb_number' => 'J-05868974', 'bpkb_name' => 'NURHALIMAH', 'stnk_name' => 'NURHALIMAH'],
                'answers' => VehicleExamples::car()],
            ['collateral' => [...$land, 'cbs_id' => '01.3.002238', 'credit_account' => '01.315.000102', 'collateral_type_code' => '06', 'binding_type_code' => '06', 'document_number' => 'No. 612',
                'description' => 'SERTIFIKAT TANAH DAN BANGUNAN NO 612 LUAS 67 M2', 'owner_name' => '1. KURNESAH 2. RASTAM BIN JUNITA', 'owner_address' => 'DUSUN LENGKONG BARAT RT/RW 11/04 LENGKONGJAYA PAMANUKAN SUBANG',
                'guarantee_value' => 13_000_000, 'fair_value' => 100_000_000, 'njop_value' => 13_000_000, 'appraisal_value' => 87_100_000, 'appraised_at' => '2026-10-15', 'appraiser_name' => 'HARI AGUNG',
                'insurance_date' => '2026-10-15', 'certificate_land_area' => 67, 'right_kind' => 'SHM'], 'answers' => LandExamples::certificateWithoutBinding()],
            ['collateral' => [...$vehicle, 'cbs_id' => '01.3.001872', 'credit_account' => '01.302.000929', 'document_number' => 'No. S-02006684',
                'description' => 'BPKB R2, MH1JM5119MK941853, JM51E1941070, T 3197 XE', 'owner_name' => 'MELVIN ANGGARA RAMDHAN', 'owner_address' => self::HAMLET,
                'guarantee_value' => 14_000_000, 'fair_value' => 14_000_000, 'njop_value' => 0, 'appraisal_value' => 14_000_000, 'appraised_at' => '2026-08-07', 'appraiser_name' => 'AHMAD FAUZI',
                'insurance_code' => 'T', 'insurance_date' => '2026-08-07', 'vehicle_kind' => 'wheel2', 'vehicle_brand' => 'HONDA', 'vehicle_model' => 'MOTOR', 'manufacture_year' => 2021,
                'plate_number' => 'T 3197 XE', 'chassis_number' => 'MH1JM5119MK941853', 'engine_number' => 'JM51E1941070', 'bpkb_number' => 'S-02006684', 'bpkb_name' => 'MELVIN ANGGARA RAMDHAN', 'stnk_name' => 'MELVIN ANGGARA RAMDHAN'],
                'answers' => VehicleExamples::motorcycle()],
            // The two supporting documents have no value, and the general form does not accept a collateral without one: they are not appraised.
            ['collateral' => [...$document, 'cbs_id' => '01.3.001871', 'credit_account' => '01.302.000929', 'description' => 'KARTU JAMSOSTEK A/N MELVIN ANGGARA RAMDHAN NO 24054390430',
                'owner_name' => 'MELVIN ANGGARA RAMDHAN', 'owner_address' => self::HAMLET]],
            ['collateral' => [...$document, 'cbs_id' => '01.3.001873', 'credit_account' => '01.302.000929', 'description' => 'SK PEGAWAI NO 91/SK.DIR/PBA/II/2024',
                'owner_name' => 'MELVIN ANGGARA RAMDHAN', 'owner_address' => self::HAMLET]],
        ];
    }

    /**
     * Fills the appraisal section by section through the same classes the screens use, and finalises it.
     *
     * @param  array<string, string|array{0: string, 1: string}>  $answers
     */
    private function appraise(Collateral $collateral, array $answers, string $purpose): void
    {
        $by = User::role(RoleName::SuperAdmin->value)->first() ?? User::query()->first() ?? throw new RuntimeException('Seed the people first: an appraisal needs someone to start it.');
        $appraisal = AppraisalStarter::start($collateral, $by, $purpose, null, null);
        $template = AppraisalTemplates::of($appraisal);

        foreach ($this->bySection($template, $answers) as $section => $sectionAnswers) {
            AppraisalSaver::save($appraisal, $template, $section, $sectionAnswers);
        }

        $blockers = AppraisalFinalisation::finalize($appraisal, $template, 'PENILAI CONTOH');

        if ($blockers !== []) {
            throw new RuntimeException("The sample appraisal of {$collateral->cbs_id} cannot be finalised: ".implode(' ', $blockers));
        }
    }

    /**
     * @param  array<string, string|array{0: string, 1: string}>  $answers
     * @return array<string, array<string, array{value: string, note: ?string}>>
     */
    private function bySection(AppraisalTemplate $template, array $answers): array
    {
        $sections = collect(AppraisalTemplates::items($template))->pluck('section', 'key');
        $grouped = [];

        foreach ($answers as $key => $answer) {
            $section = $sections[$key] ?? throw new RuntimeException("The sample answer {$key} is not an item of the {$template->key()} form.");
            [$value, $note] = is_array($answer) ? $answer : [$answer, null];
            $grouped[$section][$key] = ['value' => $value, 'note' => $note];
        }

        return $grouped;
    }
}
