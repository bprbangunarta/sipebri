<?php

namespace Tests\Feature;

use App\Models\CollateralSimulation;
use App\Models\CollateralType;
use App\Models\CommitteePath;
use App\Models\Installment;
use App\Models\LoanApplication;
use App\Models\Method;
use App\Models\Office;
use App\Models\Product;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Pengajuan Kredit — alur tahap 1: lookup KTP, store, tab pengajuan, jaminan,
 * surveyor, konfirmasi, otorisasi, dan bentuk tabel loan_applications.
 */
class LoanApplicationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function superadmin(): User
    {
        $user = User::where('username', 'superadmin')->first();
        if (! $user) {
            $user = User::factory()->create();
        }
        $user->givePermissionTo(
            Permission::findOrCreate('loan-simulation.view', 'web'),
            Permission::findOrCreate('loan-simulation.manage', 'web'),
            Permission::findOrCreate('collateral-simulation.view', 'web'),
        );

        return $user;
    }

    private function makeCollateral(): CollateralSimulation
    {
        CollateralType::firstOrCreate(['code' => '05'], ['name' => 'TANAH DAN BANGUNAN']);
        Region::firstOrCreate(['code' => '0121'], [
            'regency' => 'Kab. Subang', 'district' => 'Subang', 'village' => 'Subang',
        ]);

        return CollateralSimulation::create([
            'collateral_type_code' => '05',
            'document_number' => 'TEST-DOC-LOAN',
            'description' => 'TEST agunan pengajuan',
            'owner_name' => 'TEST owner',
            'owner_address' => 'TEST alamat',
            'region_code' => '0121',
            'region_label' => '0121 · Kab. Subang',
            'appraisal_value' => 50000000,
        ]);
    }

    /** Skema: kolom identitas pemohon dihapus, kolom tahapan baru ada. */
    public function test_schema_reshaped(): void
    {
        $removed = ['birth_place', 'birth_date', 'gender', 'marital_status', 'mother_name', 'npwp',
            'address', 'region_code', 'region_label', 'phone', 'email', 'occupation', 'employer_name',
            'monthly_income', 'other_income', 'monthly_expense', 'spouse_name', 'spouse_nik', 'spouse_income'];
        foreach ($removed as $col) {
            $this->assertFalse(Schema::hasColumn('loan_applications', $col), "kolom {$col} masih ada");
        }
        foreach (['institution_id', 'tenor_principal', 'tenor_interest', 'usage_type', 'note',
            'supervisor_id', 'surveyor_id', 'confirmed_at', 'confirmed_by'] as $col) {
            $this->assertTrue(Schema::hasColumn('loan_applications', $col), "kolom {$col} belum ada");
        }
    }

    public function test_lookup_unknown_and_known_nik(): void
    {
        $this->actingAs($this->superadmin());

        $miss = $this->getJson('/loan-simulation/lookup?nik=1111222233334444');
        $miss->assertOk()->assertJson(['found' => false]);
        $this->assertStringContainsString('belum terdaftar', $miss->json('message'));

        $hit = $this->getJson('/loan-simulation/lookup?nik=3213011203950001');
        $hit->assertOk()->assertJson(['found' => true]);
        $this->assertSame('YAYAT SUHAYAT', $hit->json('customer.full_name'));
        $this->assertSame('CIF-000123', $hit->json('customer.cif_number'));
    }

    public function test_store_rejects_unregistered_nik(): void
    {
        $this->actingAs($this->superadmin());
        $before = LoanApplication::withTrashed()->count();

        $this->from('/loan-simulation/create')
            ->post('/loan-simulation', ['nik' => '1111222233334444', 'requested_amount' => 1000000, 'requested_tenor' => 12])
            ->assertSessionHasErrors('nik');

        $this->assertSame($before, LoanApplication::withTrashed()->count());
    }

    public function test_store_validates_nik_digits(): void
    {
        $this->actingAs($this->superadmin());
        $this->post('/loan-simulation', ['nik' => '123'])->assertSessionHasErrors('nik');
    }

    /** Alur penuh: buka berkas → data pengajuan → jaminan → surveyor → konfirmasi. */
    public function test_full_flow(): void
    {
        $user = $this->superadmin();
        $this->actingAs($user);

        $expectedCode = LoanApplication::nextCode();
        // Modal pengajuan baru hanya mengirim nomor KTP.
        $this->post('/loan-simulation', ['nik' => '3213011203950001'])->assertRedirect();

        $record = LoanApplication::latest('id')->first();
        $this->assertSame($expectedCode, $record->application_code);
        $this->assertSame(8, strlen($record->application_code));
        $this->assertGreaterThanOrEqual('00800001', $record->application_code);
        $this->assertSame('YAYAT SUHAYAT', $record->full_name);
        $this->assertSame('CIF-000123', $record->cif_number);
        $this->assertSame('DRAFT', $record->status);
        $this->assertSame(0, $record->requested_amount);

        // show page renders with customer prop
        $this->get("/loan-simulation/{$record->id}")->assertOk();

        // Tab Data Pengajuan — produk wajib
        $this->put("/loan-simulation/{$record->id}", [
            'application_date' => '2026-07-01',
            'requested_amount' => 30000000,
            'requested_tenor' => 36,
        ])->assertSessionHasErrors('product_id');

        $path = CommitteePath::where('is_active', true)->firstOrFail();
        $product = Product::findOrFail($path->product_id);
        $office = Office::firstOrFail();
        $supervisor = User::whereHas('roles', fn ($q) => $q->where('name', 'Kasi Analis'))->firstOrFail();
        $payload = [
            'application_date' => '2026-07-01',
            'product_id' => $product->id,
            'committee_path_id' => $path->id,
            'requested_amount' => 30000000,
            'requested_tenor' => 36,
            'method_id' => Method::firstOrFail()->id,
            'installment_id' => Installment::firstOrFail()->id,
            'interest_rate' => 1.5,
            'usage_type' => 'KONSUMTIF',
            'office_id' => $office->id,
            'supervisor_id' => $supervisor->id,
            'marketing' => 'agus setiawan',
        ];

        $this->put("/loan-simulation/{$record->id}", $payload)->assertSessionHasNoErrors();

        $record->refresh();
        $this->assertSame($product->id, $record->product_id);
        $this->assertSame($path->id, $record->committee_path_id);
        $this->assertSame('KONSUMTIF', $record->usage_type);
        $this->assertSame('AGUS SETIAWAN', $record->marketing);
        $this->assertSame($supervisor->id, $record->supervisor_id);

        // usage_type invalid ditolak
        $this->put("/loan-simulation/{$record->id}", [...$payload, 'usage_type' => 'SALAH'])
            ->assertSessionHasErrors('usage_type');

        // Tab Jaminan — lekatkan dua kali tidak duplikat
        $collateral = $this->makeCollateral();
        $this->post("/loan-simulation/{$record->id}/collaterals", ['collateral_simulation_id' => $collateral->id])
            ->assertSessionHasNoErrors();
        $this->post("/loan-simulation/{$record->id}/collaterals", ['collateral_simulation_id' => $collateral->id])
            ->assertSessionHasNoErrors();
        $this->assertSame(1, $record->collaterals()->count());

        // Agunan baru langsung dari berkas
        $this->post("/loan-simulation/{$record->id}/collaterals/new", [
            'collateral_type_code' => CollateralType::firstOrFail()->code,
            'document_number' => 'shm 123',
            'owner_name' => 'yayat suhayat',
            'owner_address' => 'kampung cicariu',
            'region_code' => Region::firstOrFail()->code,
            'description' => 'tanah dan bangunan',
        ])->assertSessionHasNoErrors();
        $this->assertSame(2, $record->collaterals()->count());

        // Ajukan
        $this->post("/loan-simulation/{$record->id}/confirm")->assertSessionHas('success');
        $record->refresh();
        $this->assertSame('DIAJUKAN', $record->status);
        $this->assertNotNull($record->confirmed_at);
        $this->assertSame($user->id, $record->confirmed_by);

        // Filter status pada daftar
        $this->get('/loan-simulation?status=DIAJUKAN')->assertOk();
        $this->get('/loan-simulation?search='.$record->application_code)->assertOk();

        // Lepas agunan
        $this->delete("/loan-simulation/{$record->id}/collaterals/{$collateral->id}")->assertSessionHasNoErrors();
        $this->assertSame(1, $record->collaterals()->count());

        // Hapus (arsip)
        $this->delete("/loan-simulation/{$record->id}")->assertRedirect('/loan-simulation');
        $this->assertSoftDeleted('loan_applications', ['id' => $record->id]);
    }

    /** BUG: confirmed_at tidak ada di $casts → halaman berkas 500 setelah dikonfirmasi. */
    public function test_show_after_confirm_does_not_crash(): void
    {
        $user = $this->superadmin();
        $this->actingAs($user);
        $this->post('/loan-simulation', ['nik' => '3213011203950001', 'requested_amount' => 1000000, 'requested_tenor' => 12]);
        $record = LoanApplication::latest('id')->first();
        $record->forceFill([
            'product_id' => Product::first()->id,
            'office_id' => Office::first()->id,
            'status' => 'ANALISA',
            'confirmed_at' => now(),
            'confirmed_by' => $user->id,
        ])->save();
        $record->collaterals()->syncWithoutDetaching([$this->makeCollateral()->id]);

        $this->withoutExceptionHandling();
        $this->get("/loan-simulation/{$record->id}")->assertOk();
    }

    /** Konfirmasi ditolak bila tahapan belum lengkap. */
    public function test_confirm_blocked_when_incomplete(): void
    {
        $this->actingAs($this->superadmin());
        $this->post('/loan-simulation', ['nik' => '3213012509880007', 'requested_amount' => 5000000, 'requested_tenor' => 12]);
        $record = LoanApplication::latest('id')->first();

        $this->post("/loan-simulation/{$record->id}/confirm")->assertSessionHas('error');
        $this->assertSame('DRAFT', $record->refresh()->status);

        $record->forceDelete();
    }

    /** scopeBindings: agunan milik berkas lain tidak bisa dilepas. */
    public function test_detach_scoped_to_own_application(): void
    {
        $this->actingAs($this->superadmin());
        $collateral = $this->makeCollateral();

        $this->post('/loan-simulation', ['nik' => '3213011203950001', 'requested_amount' => 1000000, 'requested_tenor' => 12]);
        $a = LoanApplication::latest('id')->first();
        $this->post('/loan-simulation', ['nik' => '3213012509880007', 'requested_amount' => 1000000, 'requested_tenor' => 12]);
        $b = LoanApplication::latest('id')->first();

        $this->post("/loan-simulation/{$a->id}/collaterals", ['collateral_simulation_id' => $collateral->id]);

        // B tidak memiliki agunan ini → harus 404
        $this->delete("/loan-simulation/{$b->id}/collaterals/{$collateral->id}")->assertNotFound();
        $this->assertSame(1, $a->collaterals()->count());

        $a->collaterals()->detach();
        $a->forceDelete();
        $b->forceDelete();
    }

    /** Otorisasi: pengguna tanpa izin loan-simulation.view mendapat 403. */
    public function test_unauthorized_user_gets_403(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get('/loan-simulation')->assertForbidden();
    }

    /** Regresi modul agunan. */
    public function test_collateral_simulation_still_ok(): void
    {
        $this->actingAs($this->superadmin());
        $this->get('/collateral-simulation')->assertOk();
    }
}
