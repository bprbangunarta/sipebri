<?php

namespace Tests\Feature;

use App\Models\BindingType;
use App\Models\CollateralCondition;
use App\Models\CollateralMethod;
use App\Models\CollateralSimulation;
use App\Models\CollateralType;
use App\Models\Region;
use App\Models\SchemaDraft;
use App\Models\User;
use App\Support\SchemaDesign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/** Modul Agunan Kredit: auto Agunan ID, unique, rename kolom, payload CBS. */
class CollateralSimulationTest extends TestCase
{
    use RefreshDatabase;

    // Seed wajib true: RefreshDatabase pada sqlite :memory: hanya bermigrasi/seed sekali per run,
    // sehingga kelas pertama yang berjalan menentukan apakah seluruh suite mendapat data awal.
    protected bool $seed = true;

    private function actor(): User
    {
        $user = User::factory()->create();
        $user->givePermissionTo(
            Permission::findOrCreate('collateral-simulation.view', 'web'),
            Permission::findOrCreate('collateral-simulation.manage', 'web'),
        );

        return $user;
    }

    private function refs(): void
    {
        CollateralType::firstOrCreate(['code' => '05'], ['name' => 'TANAH DAN BANGUNAN']);
        BindingType::firstOrCreate(['code' => '01'], ['name' => 'SHT']);
        CollateralCondition::firstOrCreate(['code' => '9'], ['name' => 'BAIK']);
        CollateralMethod::firstOrCreate(['code' => '1'], ['name' => 'NILAI TAKSASI']);
        Region::firstOrCreate(['code' => '0121'], [
            'regency' => 'Kab. Subang',
            'district' => 'Subang',
            'village' => 'Subang',
        ]);
    }

    private function payload(array $override = []): array
    {
        return [
            'collateral_type_code' => '05',
            'binding_type_code' => '01',
            'document_number' => 'TEST-DOC-1',
            'description' => 'TEST agunan',
            'owner_name' => 'TEST owner',
            'owner_address' => 'TEST alamat',
            'region_code' => '0121',
            'region_label' => '0121 · Kab. Subang',
            ...$override,
        ];
    }

    // --- Skema: rename kolom sudah diterapkan ---
    public function test_table_has_new_columns_and_unique_collateral_id(): void
    {
        $new = ['guarantee_value', 'fair_value', 'njop_value', 'adjustment_value', 'appraisal_value',
            'independent_value', 'independent_name', 'independent_at', 'insurance_date', 'insurance_code'];
        $old = ['value_guarantee', 'value_fair', 'value_njop', 'value_adjustment', 'value_appraisal',
            'value_independent', 'independent_appraiser_name', 'independent_appraised_at', 'insurance_start_date', 'insured'];

        foreach ($new as $col) {
            $this->assertTrue(Schema::hasColumn('collateral_simulations', $col), "kolom baru {$col} tidak ada");
        }
        foreach ($old as $col) {
            $this->assertFalse(Schema::hasColumn('collateral_simulations', $col), "kolom lama {$col} masih ada");
        }

        $unique = collect(Schema::getIndexes('collateral_simulations'))
            ->where('unique', true)->pluck('columns')->flatten()->all();
        $this->assertContains('collateral_id', $unique);
    }

    // --- Store: Agunan ID tetap kosong (diisi setelah posting CBS) ---
    public function test_store_leaves_collateral_id_blank(): void
    {
        $this->refs();

        $response = $this->actingAs($this->actor())
            ->post('/collateral-simulation', $this->payload(['collateral_id' => '']));

        $response->assertRedirect(route('collateral-simulation.index'));
        $created = CollateralSimulation::latest('id')->first();
        $this->assertNull($created->collateral_id);
        $this->assertNull($created->credit_account);
        $this->assertSame('T', $created->insurance_code);
        $this->assertSame('1', (string) $created->ppap_code);
        $this->assertSame(0, $created->guarantee_value);
    }

    public function test_store_requires_mandatory_fields(): void
    {
        $this->refs();

        $this->actingAs($this->actor())
            ->post('/collateral-simulation', ['collateral_id' => ''])
            ->assertSessionHasErrors([
                'collateral_type_code', 'document_number', 'description',
                'owner_name', 'owner_address', 'region_code',
            ]);
    }

    public function test_duplicate_collateral_id_is_rejected_with_validation_error(): void
    {
        $this->refs();
        CollateralSimulation::create($this->payload(['collateral_id' => 'AGN-000001']));
        $before = CollateralSimulation::count();

        $this->actingAs($this->actor())
            ->post('/collateral-simulation', $this->payload(['collateral_id' => 'AGN-000001']))
            ->assertSessionHasErrors('collateral_id');

        $this->assertSame($before, CollateralSimulation::count());
    }

    // --- Update: nilai & tanggal tersimpan mentah ---
    public function test_update_saves_values_and_dates(): void
    {
        $this->refs();
        $record = CollateralSimulation::create($this->payload(['collateral_id' => 'AGN-000009']));
        $actor = $this->actor();

        $this->actingAs($actor)
            ->put("/collateral-simulation/{$record->id}", $this->payload([
                'collateral_id' => 'AGN-000009',
                'condition_code' => '9',
                'condition_date' => '2026-07-01',
                'insurance_code' => 'Y',
                'insurance_date' => '2026-07-02',
                'guarantee_value' => 150000000,
                'fair_value' => 140000000,
                'njop_value' => 130000000,
                'adjustment_value' => 5000000,
                'appraisal_value' => 120000000,
                'ppap_code' => '1',
            ]))
            ->assertRedirect(route('collateral-simulation.index'))
            ->assertSessionHasNoErrors();

        $record->refresh();
        $this->assertSame(150000000, $record->guarantee_value);
        $this->assertSame(140000000, $record->fair_value);
        $this->assertSame(130000000, $record->njop_value);
        $this->assertSame(5000000, $record->adjustment_value);
        $this->assertSame(120000000, $record->appraisal_value);
        // Penaksir & tgl taksasi dicatat sistem atas nama petugas yang menyimpan.
        $this->assertSame(mb_strtoupper($actor->name), $record->appraiser_name);
        $this->assertSame(now()->toDateString(), $record->appraised_at->format('Y-m-d'));
        $this->assertSame('2026-07-02', $record->insurance_date->format('Y-m-d'));
        $this->assertSame('Y', $record->insurance_code);
    }

    public function test_update_requires_analysis_fields(): void
    {
        $this->refs();
        $record = CollateralSimulation::create($this->payload(['collateral_id' => 'AGN-000010']));

        $this->actingAs($this->actor())
            ->put("/collateral-simulation/{$record->id}", $this->payload(['collateral_id' => 'AGN-000010']))
            ->assertSessionHasErrors(['condition_code', 'condition_date', 'insurance_code', 'insurance_date']);
    }

    // --- Payload CBS ---
    public function test_cbs_payload_contract(): void
    {
        $this->refs();
        $record = CollateralSimulation::create($this->payload([
            'collateral_id' => 'AGN-000011',
            'insurance_code' => 'Y',
            'insurance_date' => '2026-07-02',
            'njop_value' => 1, 'guarantee_value' => 2, 'adjustment_value' => 3,
            'fair_value' => 4, 'appraisal_value' => 5, 'independent_value' => 6,
            'appraiser_name' => 'A', 'appraised_at' => '2026-07-03',
            'independent_name' => 'B', 'independent_at' => '2026-07-04',
            'ppap_code' => '1',
        ]));

        $payload = $record->fresh()->toCbsPayload();

        $this->assertSame('Y', $payload['asuransi']);
        $this->assertSame('2026-07-02', $payload['startdate']);
        $this->assertSame(['njop' => 1, 'jaminan' => 2, 'adjust' => 3, 'wajar' => 4, 'taksasi' => 5, 'independen' => 6], $payload['nilai']);
        $this->assertSame('A', $payload['penaksir']['taksasi']['penaksir']);
        $this->assertSame('2026-07-03', $payload['penaksir']['taksasi']['tanggal']);
        $this->assertSame('B', $payload['penaksir']['independen']['penaksir']);
        $this->assertSame('2026-07-04', $payload['penaksir']['independen']['tanggal']);
        $this->assertSame(1, $payload['ppap']);
    }

    // --- Halaman index & detail ---
    public function test_index_and_show_render(): void
    {
        $this->refs();
        $record = CollateralSimulation::create($this->payload(['collateral_id' => 'AGN-000012']));
        $user = $this->actor();

        $this->actingAs($user)->get('/collateral-simulation')->assertOk();

        $response = $this->actingAs($user)->get("/collateral-simulation/{$record->id}");
        $response->assertOk();
        $names = collect($response->viewData('page')['props']['columns'])->pluck('name')->all();
        $this->assertContains('guarantee_value', $names);
        $this->assertContains('insurance_code', $names);
        $this->assertNotContains('insured', $names);
        $this->assertNotContains('value_njop', $names);
    }

    // --- Diff SchemaDesign: default, unique, index ---
    public function test_diff_detects_default_unique_and_index_changes(): void
    {
        $draft = SchemaDraft::firstOrCreate(
            ['table_name' => 'collateral_simulations'],
            ['name' => 'Agunan'],
        );
        $draft->columns()->delete();
        foreach (SchemaDesign::importFrom('collateral_simulations') as $column) {
            $draft->columns()->create($column);
        }

        $rows = collect(SchemaDesign::diff($draft->fresh('columns')));
        $this->assertEmpty($rows->where('status', '!=', 'sama')->all(), 'diff impor harus semua sama: '.$rows->where('status', '!=', 'sama')->toJson());

        // Ubah default
        $target = $draft->columns()->where('name', 'description')->first();
        $target->update(['default_value' => 'XX']);
        $row = collect(SchemaDesign::diff($draft->fresh('columns')))->firstWhere('name', 'description');
        $this->assertSame('berubah', $row['status']);
        $this->assertStringContainsString('default', $row['note']);

        $target->update(['default_value' => null, 'is_unique' => true]);
        $row = collect(SchemaDesign::diff($draft->fresh('columns')))->firstWhere('name', 'description');
        $this->assertSame('berubah', $row['status']);
        $this->assertStringContainsString('tambah unique', $row['note']);

        $target->update(['is_unique' => false, 'is_index' => true]);
        $row = collect(SchemaDesign::diff($draft->fresh('columns')))->firstWhere('name', 'description');
        $this->assertStringContainsString('tambah index', $row['note']);

        $target->update(['is_index' => false]);
        $row = collect(SchemaDesign::diff($draft->fresh('columns')))->firstWhere('name', 'description');
        $this->assertSame('sama', $row['status']);

        // Migration code untuk tabel yang sudah ada
        $code = SchemaDesign::migrationCode($draft->fresh('columns'));
        $this->assertStringContainsString("Schema::table('collateral_simulations'", $code);
        $this->assertStringNotContainsString('Schema::create', $code);
    }
}
