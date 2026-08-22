<?php

namespace Tests\Feature;

use App\Models\AnalysisAdministration;
use App\Models\AnalysisBusiness;
use App\Models\AnalysisFiveC;
use App\Models\AnalysisMemorandum;
use App\Models\CollateralSimulation;
use App\Models\CommitteePath;
use App\Models\Installment;
use App\Models\LoanApplication;
use App\Models\Office;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Analisa Usaha (bagian 1) + Analisa Keuangan & Kepemilikan (bagian 2 & 3).
 * Rumus diverifikasi memakai angka contoh dari sistem lama.
 */
class AnalysisBusinessTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function staff(): User
    {
        return User::role('Staff Analis')->firstOrFail();
    }

    private function surveyed(?User $surveyor = null): LoanApplication
    {
        return LoanApplication::create([
            'application_code' => LoanApplication::nextCode(),
            'application_date' => now()->toDateString(),
            'status' => 'SURVEY',
            'nik' => '3213011203950001',
            'full_name' => 'YAYAT SUHAYAT',
            'product_id' => Product::first()->id,
            'committee_path_id' => CommitteePath::first()->id,
            'office_id' => Office::first()->id,
            'supervisor_id' => User::role('Kasi Analis')->firstOrFail()->id,
            'surveyor_id' => ($surveyor ?? $this->staff())->id,
            'requested_amount' => 30_000_000,
            'requested_tenor' => 36,
            'created_by' => 'IT Support',
        ]);
    }

    public function test_business_code_is_generated_per_type(): void
    {
        $app = $this->surveyed();

        $this->actingAs($this->staff())
            ->post("/analysis-simulation/{$app->id}/businesses", ['type' => 'PERDAGANGAN', 'name' => 'warung klontongan'])
            ->assertRedirect();

        $business = AnalysisBusiness::firstOrFail();

        $this->assertSame('AUPG00001', $business->code);
        $this->assertSame('WARUNG KLONTONGAN', $business->name);
    }

    public function test_trade_metrics_follow_legacy_example(): void
    {
        $app = $this->surveyed();
        $business = AnalysisBusiness::create([
            'loan_application_id' => $app->id,
            'type' => 'PERDAGANGAN',
            'code' => AnalysisBusiness::nextCode('PERDAGANGAN'),
            'name' => 'WARUNG KLONTONGAN',
        ]);

        $this->actingAs($this->staff())->put("/analysis-simulation/{$app->id}/businesses/{$business->id}", [
            'groups' => ['GOODS'],
            'items' => [
                ['group' => 'GOODS', 'name' => 'LE MINERAL', 'price' => 2200, 'sell_price' => 3000, 'qty' => 0],
                ['group' => 'GOODS', 'name' => 'KOPI KAPAL API', 'price' => 1000, 'sell_price' => 1500, 'qty' => 0],
                ['group' => 'GOODS', 'name' => 'KACANG', 'price' => 700, 'sell_price' => 1000, 'qty' => 0],
                ['group' => 'GOODS', 'name' => 'ROTI', 'price' => 1500, 'sell_price' => 2000, 'qty' => 0],
                ['group' => 'GOODS', 'name' => 'MALKIS', 'price' => 700, 'sell_price' => 1000, 'qty' => 0],
                ['group' => 'GOODS', 'name' => 'KOPI ABC', 'price' => 1000, 'sell_price' => 1500, 'qty' => 0],
                ['group' => 'GOODS', 'name' => 'GOOD DAY', 'price' => 1000, 'sell_price' => 1500, 'qty' => 0],
                ['group' => 'GOODS', 'name' => 'BARANG LAINNYA', 'price' => 8800, 'sell_price' => 11500, 'qty' => 0],
            ],
        ])->assertRedirect();

        $this->actingAs($this->staff())->put("/analysis-simulation/{$app->id}/businesses/{$business->id}", [
            'daily_purchase' => 350_000,
            'cost_of_goods' => 350_000,
            'transport_cost' => 10_000,
            'gatel_cost' => 5_000,
        ])->assertRedirect();

        $metrics = $business->refresh()->metrics();

        $this->assertSame(36.09, $metrics['margin_percent']);
        $this->assertSame(476_315, $metrics['daily_revenue']);
        $this->assertSame(126_315, $metrics['daily_profit']);
        $this->assertSame(3_789_450, $metrics['monthly_profit']);
        $this->assertSame(450_000, $metrics['monthly_cost']);
        $this->assertSame(3_339_450, $business->net_profit);
    }

    public function test_farm_metrics_follow_legacy_example(): void
    {
        $app = $this->surveyed();
        $business = AnalysisBusiness::create([
            'loan_application_id' => $app->id,
            'type' => 'PERTANIAN',
            'code' => AnalysisBusiness::nextCode('PERTANIAN'),
            'name' => 'PERTANIAN PADI',
        ]);

        $this->actingAs($this->staff())->put("/analysis-simulation/{$app->id}/businesses/{$business->id}", [
            'area_pawn' => 10_000,
            'harvest_kw' => 65,
            'price_per_kw' => 650_000,
            'cost_land' => 3_142_857,
            'cost_seed' => 278_571,
            'cost_fertilizer' => 2_350_000,
            'cost_pesticide' => 728_571,
            'cost_labor' => 2_042_857,
            'cost_harvest' => 4_471_429,
            'cost_tax' => 1_428_571,
            'cost_village' => 714_286,
        ])->assertRedirect();

        $metrics = $business->refresh()->metrics();

        $this->assertSame(10_000, $metrics['total_area']);
        $this->assertSame(42_250_000, $metrics['harvest_income']);
        $this->assertSame(15_157_142, $metrics['total_cost']);
        $this->assertSame(27_092_858, $business->net_profit);
        // Angsuran pokok otomatis: plafon 30jt ÷ 36 bln × 6 bln = 5jt.
        $this->assertSame(5_000_000, $metrics['principal_installment']);
        $this->assertSame(3_682_143, $metrics['monthly_income']);
        // Kontribusi pertanian ke Analisa Keuangan dihitung PER BULAN.
        $this->assertSame(3_682_143, (int) $business->monthly_income);
    }

    /** Contoh nyata sistem lama: KARIM, KBT FLAT, plafon 28 jt / 12 bulan (AUP00564). */
    public function test_farm_matches_legacy_karim_example(): void
    {
        $app = $this->surveyed();
        $app->update(['requested_amount' => 28_000_000, 'requested_tenor' => 12]);

        $business = AnalysisBusiness::create([
            'loan_application_id' => $app->id,
            'type' => 'PERTANIAN',
            'code' => AnalysisBusiness::nextCode('PERTANIAN'),
            'name' => 'PERTANIAN PADI',
        ]);

        $this->actingAs($this->staff())->put("/analysis-simulation/{$app->id}/businesses/{$business->id}", [
            'area_own' => 5_627,
            'area_rent' => 0,
            'area_pawn' => 17_500,
            'harvest_kw' => 165,
            'price_per_kw' => 680_000,
            'cost_land' => 7_268_486,
            'cost_seed' => 644_252,
            'cost_harvest' => 10_341_073,
            'cost_fertilizer' => 5_434_845,
            'cost_pesticide' => 1_684_967,
            'cost_tax' => 3_303_857,
            'cost_village' => 1_651_929,
            'cost_labor' => 4_724_516,
            'cost_other_bank' => 52_125_000,
        ])->assertRedirect();

        $metrics = $business->refresh()->metrics();

        $this->assertSame(23_127, $metrics['total_area']);
        $this->assertSame(112_200_000, $metrics['harvest_income']);
        $this->assertSame(87_178_925, $metrics['total_cost']);
        $this->assertSame(25_021_075, $business->net_profit);
        $this->assertSame(14_000_000, $metrics['principal_installment']);
        $this->assertSame(1_836_845, $metrics['monthly_income']);

        // Penambahan hasil usaha ditambahkan SETELAH pembagian 6 bulan.
        $this->actingAs($this->staff())->put("/analysis-simulation/{$app->id}/businesses/{$business->id}", [
            'addition_result' => 12_000_000,
        ])->assertRedirect();

        $this->assertSame(13_836_845, $business->refresh()->metrics()['monthly_income']);
    }

    public function test_finance_sheet_sums_business_net_profit(): void
    {
        $app = $this->surveyed();

        AnalysisBusiness::create([
            'loan_application_id' => $app->id,
            'type' => 'PERDAGANGAN',
            'code' => AnalysisBusiness::nextCode('PERDAGANGAN'),
            'name' => 'WARUNG KLONTONGAN',
            'net_profit' => 3_339_450,
            'monthly_income' => 3_339_450,
        ]);

        $this->actingAs($this->staff())->put("/analysis-simulation/{$app->id}/finance", [
            'cost_staple' => 1_000_000,
            'cost_education' => 100_000,
            'cost_children' => 300_000,
            'cost_cigarette' => 200_000,
            'cost_health' => 150_000,
            'cost_gatel' => 300_000,
            'cost_social' => 20_000,
            'items' => [['name' => 'koperasi', 'amount' => 0]],
        ])->assertRedirect();

        $metrics = $app->analysisSheet->refresh()->metrics();

        $this->assertSame(3_339_450, $metrics['business_income']);
        $this->assertSame(2_070_000, $metrics['household_cost']);
        $this->assertSame(1_269_450, $metrics['monthly_balance']);
        $this->assertDatabaseHas('analysis_sheet_items', ['group' => 'OBLIGATION', 'name' => 'KOPERASI']);
    }

    /**
     * Contoh nyata sistem lama (KARIM): pertanian (pendapatan perbulan 1.836.845)
     * + jasa mobil angkutan (hasil bersih 3.900.000) → Keuangan Perbulan 1.262.207.
     */
    public function test_finance_sheet_matches_legacy_karim_example(): void
    {
        $app = $this->surveyed();
        $app->update(['requested_amount' => 28_000_000, 'requested_tenor' => 12]);

        $farm = AnalysisBusiness::create([
            'loan_application_id' => $app->id,
            'type' => 'PERTANIAN',
            'code' => AnalysisBusiness::nextCode('PERTANIAN'),
            'name' => 'PERTANIAN PADI',
            'harvest_kw' => 165,
            'price_per_kw' => 680_000,
            'cost_land' => 7_268_486,
            'cost_seed' => 644_252,
            'cost_harvest' => 10_341_073,
            'cost_fertilizer' => 5_434_845,
            'cost_pesticide' => 1_684_967,
            'cost_tax' => 3_303_857,
            'cost_village' => 1_651_929,
            'cost_labor' => 4_724_516,
            'cost_other_bank' => 52_125_000,
        ]);
        $farm->recalculate();
        $farm->save();

        $service = AnalysisBusiness::create([
            'loan_application_id' => $app->id,
            'type' => 'JASA',
            'code' => AnalysisBusiness::nextCode('JASA'),
            'name' => 'JASA MOBIL ANGKUTAN',
            'service_income' => 4_500_000,
            'vehicle_tax' => 600_000,
        ]);
        $service->recalculate();
        $service->save();

        $this->assertSame(1_836_845, (int) $farm->monthly_income);
        $this->assertSame(3_900_000, (int) $service->monthly_income);

        $this->actingAs($this->staff())->put("/analysis-simulation/{$app->id}/finance", [
            'cost_staple' => 1_300_000,
            'cost_education' => 300_000,
            'cost_children' => 200_000,
            'cost_cigarette' => 250_000,
            'cost_health' => 300_000,
            'cost_gatel' => 500_000,
            'cost_social' => 50_000,
            'items' => [
                ['name' => 'REKAP SLIK', 'amount' => 1_374_638],
                ['name' => 'PAJAK KENDARAAN', 'amount' => 200_000],
            ],
        ])->assertRedirect();

        $metrics = $app->analysisSheet->refresh()->metrics();

        $this->assertSame(1_836_845, $metrics['farm_income']);
        $this->assertSame(3_900_000, $metrics['service_income']);
        $this->assertSame(5_736_845, $metrics['business_income']);
        $this->assertSame(2_900_000, $metrics['household_cost']);
        $this->assertSame(1_574_638, $metrics['obligation_cost']);
        $this->assertSame(1_262_207, $metrics['monthly_balance']);
    }

    /** Periode setoran mengikuti kelipatan sistem cicilan berkas. */
    public function test_farm_uses_installment_period_of_application(): void
    {
        $app = $this->surveyed();
        $app->update([
            'requested_amount' => 28_000_000,
            'requested_tenor' => 12,
            'installment_id' => Installment::where('name', 'MUSIMAN')->value('id'),
        ]);

        $business = AnalysisBusiness::create([
            'loan_application_id' => $app->id,
            'type' => 'PERTANIAN',
            'code' => AnalysisBusiness::nextCode('PERTANIAN'),
            'name' => 'PERTANIAN PADI',
            'harvest_kw' => 165,
            'price_per_kw' => 680_000,
            'cost_land' => 7_268_486,
            'cost_seed' => 644_252,
            'cost_harvest' => 10_341_073,
            'cost_fertilizer' => 5_434_845,
            'cost_pesticide' => 1_684_967,
            'cost_tax' => 3_303_857,
            'cost_village' => 1_651_929,
            'cost_labor' => 4_724_516,
            'cost_other_bank' => 52_125_000,
        ]);

        // MUSIMAN: setoran tiap 6 bulan → 2 kali setor.
        $metrics = $business->metrics();
        $this->assertSame(6, $metrics['installment_period']);
        $this->assertSame(14_000_000, $metrics['principal_installment']);
        $this->assertSame(11_021_075, $metrics['after_principal']);
        $this->assertSame(1_836_845, $metrics['monthly_income']);

        // BULANAN: setoran tiap bulan → 12 kali setor, tanpa pembagian 6 lagi.
        $app->update(['installment_id' => Installment::where('name', 'BULANAN')->value('id')]);
        $metrics = $business->refresh()->metrics();

        $this->assertSame(1, $metrics['installment_period']);
        $this->assertSame(2_333_333, $metrics['principal_installment']);
        $this->assertSame(22_687_742, $metrics['monthly_income']);
    }

    public function test_five_c_scores_are_saved_and_evaluated(): void
    {
        $app = $this->surveyed();

        $this->actingAs($this->staff())->put("/analysis-simulation/{$app->id}/five-c", [
            'gaya_hidup' => 3,
            'pengendalian_emosi' => 3,
            'perbuatan_tercela' => 2,
            'kondisi_alam' => 5,
            'persaingan_usaha' => 3,
            'regulasi_pemerintah' => 4,
        ])->assertRedirect();

        $metrics = AnalysisFiveC::firstOrFail()->metrics();

        // Character: 8 dari 9 → 88,89% BAIK. Condition: 12 dari 12 → 100% BAIK.
        $this->assertSame(88.89, $metrics['groups']['character']['percent']);
        $this->assertSame('BAIK', $metrics['groups']['character']['grade']);
        $this->assertSame(100.0, $metrics['groups']['condition']['percent']);
        $this->assertNull($metrics['groups']['capital']['grade']);
        $this->assertSame('BAIK', $metrics['grade']);
    }

    public function test_five_c_rejects_score_above_scale(): void
    {
        $app = $this->surveyed();

        $this->actingAs($this->staff())
            ->put("/analysis-simulation/{$app->id}/five-c", ['gaya_hidup' => 5])
            ->assertSessionHasErrors('gaya_hidup');
    }

    public function test_qualitative_is_saved_uppercase_and_validated(): void
    {
        $app = $this->surveyed();

        $this->actingAs($this->staff())->put("/analysis-simulation/{$app->id}/qualitative", [
            'bi_checking' => 4,
            'hubungan_tetangga' => 'BAIK',
            'kewajiban1' => 'BPR',
            'status1' => 'LANCAR',
            'ket_kewajiban1' => 'angsuran motor',
            'kekuatan' => 'lokasi usaha strategis',
            'catatan' => 'usaha layak dibiayai',
        ])->assertRedirect();

        $this->assertDatabaseHas('analysis_qualitative', [
            'loan_application_id' => $app->id,
            'bi_checking' => 4,
            'ket_kewajiban1' => 'ANGSURAN MOTOR',
            'kekuatan' => 'LOKASI USAHA STRATEGIS',
        ]);

        $this->actingAs($this->staff())
            ->put("/analysis-simulation/{$app->id}/qualitative", ['status1' => 'HAMPIR MACET'])
            ->assertSessionHasErrors('status1');
    }

    public function test_memorandum_and_administration_are_saved(): void
    {
        $app = $this->surveyed();

        $this->actingAs($this->staff())->put("/analysis-simulation/{$app->id}/memorandum", [
            'modal_kerja' => 20_000_000,
            'investasi' => 10_000_000,
            'ket_modal_kerja' => 'tambah stok barang',
            'usulan_plafond' => 30_000_000,
            'jangka_waktu' => 36,
            's_bunga' => 1.5,
            'pengikatan' => 'NOTARIIL',
        ])->assertRedirect();

        $memo = AnalysisMemorandum::firstOrFail();
        $this->assertSame(30_000_000, $memo->totalNeed());
        $this->assertSame('TAMBAH STOK BARANG', $memo->ket_modal_kerja);

        $this->actingAs($this->staff())
            ->put("/analysis-simulation/{$app->id}/memorandum", ['pengikatan' => 'SALAH'])
            ->assertSessionHasErrors('pengikatan');

        $this->actingAs($this->staff())->put("/analysis-simulation/{$app->id}/administration", [
            'administrasi' => 150_000,
            'provisi' => 300_000,
            'materai' => 20_000,
        ])->assertRedirect();

        $this->assertSame(470_000, AnalysisAdministration::firstOrFail()->total());
    }

    public function test_collateral_check_only_accepts_collateral_of_the_application(): void
    {
        $app = $this->surveyed();
        $collateral = CollateralSimulation::create([
            'document_number' => '99887766',
            'collateral_type_code' => '05',
            'description' => 'SERTIFIKAT TANAH UJI',
            'owner_name' => 'YAYAT SUHAYAT',
            'created_by' => 'IT Support',
        ]);
        $app->collaterals()->sync([$collateral->id]);

        $this->actingAs($this->staff())->put("/analysis-simulation/{$app->id}/collaterals", [
            'rows' => [[
                'collateral_simulation_id' => $collateral->id,
                'kind' => 'TANAH',
                'luas' => 5_711,
                'lokasi' => 'arjasari patrol indramayu',
                'appraisal_value' => 60_000_000,
            ]],
        ])->assertRedirect();

        $this->assertDatabaseHas('analysis_collaterals', [
            'loan_application_id' => $app->id,
            'kind' => 'TANAH',
            'luas' => 5_711,
            'lokasi' => 'ARJASARI PATROL INDRAMAYU',
            'appraisal_value' => 60_000_000,
        ]);

        $this->actingAs($this->staff())->put("/analysis-simulation/{$app->id}/collaterals", [
            'rows' => [['collateral_simulation_id' => 999, 'kind' => 'TANAH']],
        ])->assertSessionHasErrors('rows.0.collateral_simulation_id');
    }

    public function test_ownership_rejects_unknown_option(): void
    {
        $app = $this->surveyed();

        $this->actingAs($this->staff())
            ->put("/analysis-simulation/{$app->id}/ownership", ['asset_house' => 'GEDONG'])
            ->assertSessionHasErrors('asset_house');
    }

    public function test_other_analyst_cannot_open_business(): void
    {
        $other = User::role('Staff Analis')->where('id', '!=', $this->staff()->id)->firstOrFail();
        $app = $this->surveyed($other);

        $business = AnalysisBusiness::create([
            'loan_application_id' => $app->id,
            'type' => 'JASA',
            'code' => AnalysisBusiness::nextCode('JASA'),
            'name' => 'KARYAWAN PABRIK',
        ]);

        $this->actingAs($this->staff())
            ->get("/analysis-simulation/{$app->id}/businesses/{$business->id}")
            ->assertNotFound();
    }
}
