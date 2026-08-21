<?php

namespace Tests\Feature;

use App\Models\AnalysisBusiness;
use App\Models\AnalysisSheet;
use App\Models\CommitteePath;
use App\Models\LoanApplication;
use App\Models\Office;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cakupan tambahan modul Analisa Kredit (QA):
 * - Usaha Jasa & Lainnya (metrics + sinkron baris per group)
 * - Analisa Kepemilikan (persistensi + baris harta lain)
 * - Hapus usaha, validasi, otorisasi, sheetPayload/props Inertia
 */
class AnalysisModuleExtraTest extends TestCase
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
            'nik' => '3213011203950002',
            'full_name' => 'QA TEST PEMOHON',
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

    private function make(LoanApplication $app, string $type, string $name): AnalysisBusiness
    {
        return AnalysisBusiness::create([
            'loan_application_id' => $app->id,
            'type' => $type,
            'code' => AnalysisBusiness::nextCode($type),
            'name' => $name,
        ]);
    }

    // --- Usaha Jasa -------------------------------------------------------
    public function test_service_metrics_and_persistence(): void
    {
        $app = $this->surveyed();
        $b = $this->make($app, 'JASA', 'JASA ANGKUTAN');

        $this->actingAs($this->staff())->put("/analysis-simulation/{$app->id}/businesses/{$b->id}", [
            'service_income' => 5_000_000,
            'vehicle_tax' => 300_000,
            'other_expense' => 700_000,
        ])->assertRedirect();

        $m = $b->refresh()->metrics();

        $this->assertSame(5_000_000, $m['total_income']);
        $this->assertSame(1_000_000, $m['total_expense']);
        $this->assertSame(4_000_000, $b->net_profit);
        $this->assertSame(5_000_000, (int) $b->revenue);
        $this->assertSame(1_000_000, (int) $b->expense);
    }

    // --- Usaha Lainnya: baris INCOME & EXPENSE tidak saling terhapus -------
    public function test_other_business_income_and_expense_rows_survive_separate_saves(): void
    {
        $app = $this->surveyed();
        $b = $this->make($app, 'LAINNYA', 'KONVEKSI JAYA');
        $staff = $this->staff();

        // Simpan bahan baku
        $this->actingAs($staff)->put("/analysis-simulation/{$app->id}/businesses/{$b->id}", [
            'business_kind' => 'KONVEKSI',
            'groups' => ['MATERIAL'],
            'items' => [
                ['group' => 'MATERIAL', 'name' => 'KAIN', 'qty' => 10, 'price' => 25_000],
                ['group' => 'MATERIAL', 'name' => 'BENANG', 'qty' => 5, 'price' => 10_000],
            ],
        ])->assertRedirect();

        // Simpan keuangan (INCOME + EXPENSE dalam satu kartu)
        $this->actingAs($staff)->put("/analysis-simulation/{$app->id}/businesses/{$b->id}", [
            'groups' => ['INCOME', 'EXPENSE'],
            'projection_addition' => 500_000,
            'items' => [
                ['group' => 'INCOME', 'name' => 'PENJUALAN SERAGAM', 'price' => 8_000_000, 'qty' => 0],
                ['group' => 'EXPENSE', 'name' => 'LISTRIK', 'price' => 400_000, 'qty' => 0],
                ['group' => 'EXPENSE', 'name' => 'GAJI', 'price' => 1_600_000, 'qty' => 0],
            ],
        ])->assertRedirect();

        $b->refresh()->unsetRelation('items');
        $m = $b->metrics();

        $this->assertSame(8_000_000, $m['business_income']);
        $this->assertSame(2_000_000, $m['operational_cost']);
        $this->assertSame(300_000, $m['material_cost']); // 10*25k + 5*10k
        $this->assertSame(6_200_000, $b->net_profit); // 8jt - 2jt - 300rb + 500rb
        $this->assertCount(5, $b->items);

        // Simpan ulang hanya keuangan → baris MATERIAL harus tetap ada
        $this->actingAs($staff)->put("/analysis-simulation/{$app->id}/businesses/{$b->id}", [
            'groups' => ['INCOME', 'EXPENSE'],
            'items' => [
                ['group' => 'INCOME', 'name' => 'PENJUALAN SERAGAM', 'price' => 9_000_000, 'qty' => 0],
                ['group' => 'EXPENSE', 'name' => 'LISTRIK', 'price' => 400_000, 'qty' => 0],
            ],
        ])->assertRedirect();

        $b->refresh()->unsetRelation('items');
        $this->assertSame(2, $b->items->where('group', 'MATERIAL')->count());
        $this->assertSame(1, $b->items->where('group', 'INCOME')->count());
        $this->assertSame(1, $b->items->where('group', 'EXPENSE')->count());
        $this->assertSame(9_000_000, $b->metrics()['business_income']);
    }

    // --- Analisa Kepemilikan ---------------------------------------------
    public function test_ownership_saves_all_assets_and_extra_rows(): void
    {
        $app = $this->surveyed();

        $payload = [
            'asset_house' => 'SEMI PERMANEN',
            'asset_car' => '1 UNIT',
            'asset_motorcycle' => '2 UNIT',
            'asset_computer' => 'ADA',
            'asset_washer' => 'TIDAK ADA',
            'asset_tv' => 'CRT CEMBUNG',
            'asset_chair' => 'ADA',
            'asset_cabinet' => 'TIDAK ADA',
            'items' => [['name' => 'sawah 200 bata'], ['name' => 'kulkas']],
        ];

        $this->actingAs($this->staff())
            ->put("/analysis-simulation/{$app->id}/ownership", $payload)
            ->assertRedirect();

        $sheet = AnalysisSheet::where('loan_application_id', $app->id)->firstOrFail();

        foreach (array_keys(AnalysisSheet::ASSETS) as $key) {
            $this->assertSame($payload[$key], $sheet->{$key}, "aset {$key} tidak tersimpan");
        }

        $this->assertDatabaseHas('analysis_sheet_items', ['group' => 'ASSET', 'name' => 'SAWAH 200 BATA']);
        $this->assertSame(2, $sheet->items()->where('group', 'ASSET')->count());

        // Menyimpan keuangan tidak boleh menghapus baris harta
        $this->actingAs($this->staff())->put("/analysis-simulation/{$app->id}/finance", [
            'cost_staple' => 1_000_000,
            'items' => [['name' => 'BANK LAIN', 'amount' => 500_000]],
        ])->assertRedirect();

        $this->assertSame(2, $sheet->items()->where('group', 'ASSET')->count());
        $this->assertSame(1, $sheet->items()->where('group', 'OBLIGATION')->count());
    }

    // --- Payload lembar analisa -------------------------------------------
    public function test_sheet_payload_exposes_income_per_type(): void
    {
        $app = $this->surveyed();

        foreach ([['PERDAGANGAN', 1_000_000], ['PERTANIAN', 2_000_000], ['JASA', 3_000_000], ['LAINNYA', 4_000_000]] as [$type, $net]) {
            AnalysisBusiness::create([
                'loan_application_id' => $app->id,
                'type' => $type,
                'code' => AnalysisBusiness::nextCode($type),
                'name' => "USAHA {$type}",
                'net_profit' => $net,
                'monthly_income' => $net,
            ]);
        }

        $this->actingAs($this->staff())
            ->get("/analysis-simulation/{$app->id}")
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('AnalysisDetail')
                ->where('sheet.metrics.trade_income', 1_000_000)
                ->where('sheet.metrics.farm_income', 2_000_000)
                ->where('sheet.metrics.service_income', 3_000_000)
                ->where('sheet.metrics.other_income', 4_000_000)
                ->where('sheet.metrics.business_income', 10_000_000)
                ->has('businesses', 4)
                ->has('options.assets'));
    }

    // --- Hapus usaha ------------------------------------------------------
    public function test_business_delete_removes_row_and_items(): void
    {
        $app = $this->surveyed();
        $b = $this->make($app, 'PERDAGANGAN', 'WARUNG QA');

        $this->actingAs($this->staff())->put("/analysis-simulation/{$app->id}/businesses/{$b->id}", [
            'groups' => ['GOODS'],
            'items' => [['group' => 'GOODS', 'name' => 'GULA', 'price' => 1000, 'sell_price' => 1500, 'qty' => 1]],
        ])->assertRedirect();

        $this->actingAs($this->staff())
            ->delete("/analysis-simulation/{$app->id}/businesses/{$b->id}")
            ->assertRedirect("/analysis-simulation/{$app->id}");

        $this->assertDatabaseMissing('analysis_businesses', ['id' => $b->id]);
        $this->assertSame(0, \App\Models\AnalysisBusinessItem::where('analysis_business_id', $b->id)->count());
    }

    // --- Validasi ---------------------------------------------------------
    public function test_store_rejects_unknown_type_and_empty_name(): void
    {
        $app = $this->surveyed();

        $this->actingAs($this->staff())
            ->post("/analysis-simulation/{$app->id}/businesses", ['type' => 'TAMBANG', 'name' => 'X'])
            ->assertSessionHasErrors('type');

        $this->actingAs($this->staff())
            ->post("/analysis-simulation/{$app->id}/businesses", ['type' => 'JASA', 'name' => ''])
            ->assertSessionHasErrors('name');
    }

    public function test_update_rejects_negative_money_and_bad_option(): void
    {
        $app = $this->surveyed();
        $b = $this->make($app, 'PERTANIAN', 'PERTANIAN QA');

        $this->actingAs($this->staff())->put("/analysis-simulation/{$app->id}/businesses/{$b->id}", [
            'harvest_kw' => -5,
            'plant_type' => 'PADI ANEH',
        ])->assertSessionHasErrors(['harvest_kw', 'plant_type']);
    }

    public function test_business_length_null_is_not_stored_as_zero(): void
    {
        $app = $this->surveyed();
        $b = $this->make($app, 'JASA', 'JASA QA');

        $this->actingAs($this->staff())->put("/analysis-simulation/{$app->id}/businesses/{$b->id}", [
            'business_length' => null,
            'address' => null,
        ])->assertRedirect();

        $b->refresh();
        $this->assertTrue(
            $b->business_length === null || $b->business_length === '',
            "lama usaha kosong tersimpan sebagai '{$b->business_length}'"
        );
    }

    // --- Otorisasi --------------------------------------------------------
    public function test_other_analyst_cannot_update_or_delete(): void
    {
        $other = User::role('Staff Analis')->where('id', '!=', $this->staff()->id)->firstOrFail();
        $app = $this->surveyed($other);
        $b = $this->make($app, 'JASA', 'MILIK ORANG LAIN');

        $this->actingAs($this->staff())->put("/analysis-simulation/{$app->id}/businesses/{$b->id}", ['service_income' => 1])->assertNotFound();
        $this->actingAs($this->staff())->delete("/analysis-simulation/{$app->id}/businesses/{$b->id}")->assertNotFound();
        $this->actingAs($this->staff())->put("/analysis-simulation/{$app->id}/finance", ['cost_staple' => 1])->assertNotFound();
        $this->actingAs($this->staff())->put("/analysis-simulation/{$app->id}/ownership", ['asset_car' => '1 UNIT'])->assertNotFound();
    }

    public function test_business_of_other_application_is_not_reachable(): void
    {
        $staff = $this->staff();
        $a = $this->surveyed();
        $bOther = $this->make($this->surveyed(), 'JASA', 'USAHA BERKAS LAIN');

        // scopeBindings: usaha milik berkas lain harus 404 walau berkas milik user
        $this->actingAs($staff)
            ->get("/analysis-simulation/{$a->id}/businesses/{$bOther->id}")
            ->assertNotFound();
    }
}
