<?php

use App\Enums\LoanStatus;
use App\Models\AppNotification;
use App\Models\Collateral;
use App\Models\CollateralType;
use App\Models\CommitteePath;
use App\Models\Installment;
use App\Models\LoanApplication;
use App\Models\Method;
use App\Models\Office;
use App\Models\Product;
use App\Models\ProductParameter;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;

/**
 * A product with limits, its committee path, an office and a section head.
 *
 * @return array<string, mixed>
 */
function loanSetup(bool $collateralRequired = false): array
{
    test()->seed(RoleSeeder::class);

    $method = Method::create(['code' => '10', 'name' => 'FLAT']);
    $other = Method::create(['code' => '20', 'name' => 'EFFECTIVE']);
    $installment = Installment::create(['code' => '3', 'name' => 'MONTHLY', 'period_months' => 1]);
    $product = Product::create(['code' => '01', 'alias' => 'KRU', 'name' => 'GENERAL', 'is_active' => true]);
    ProductParameter::create([
        'product_id' => $product->id, 'min_amount' => 1_000_000, 'max_amount' => 50_000_000, 'min_tenor' => 1, 'max_tenor' => 24,
        'allowed_method_ids' => [$method->id], 'allowed_installment_ids' => [$installment->id], 'collateral_required' => $collateralRequired,
    ]);

    return [
        'product' => $product, 'method' => $method, 'otherMethod' => $other, 'installment' => $installment,
        'path' => CommitteePath::create(['product_id' => $product->id, 'mechanism' => 'plafon']),
        'office' => Office::create(['code' => '00', 'alias' => 'PMK', 'name' => 'Pamanukan']),
        'supervisor' => User::factory()->create()->assignRole('Kepala Seksi Analis'),
    ];
}

/**
 * @param  array<string, mixed>  $s
 * @return array<string, mixed>
 */
function loanPayload(array $s, array $overrides = []): array
{
    return array_merge([
        'application_date' => now()->toDateString(), 'product_id' => $s['product']->id, 'committee_path_id' => $s['path']->id,
        'requested_amount' => 10_000_000, 'requested_tenor' => 12, 'method_id' => $s['method']->id, 'installment_id' => $s['installment']->id,
        'interest_rate' => 13, 'usage_type' => 'WORKING CAPITAL', 'office_id' => $s['office']->id, 'supervisor_id' => $s['supervisor']->id,
        'marketing' => 'adi',
    ], $overrides);
}

function loanOfficer(): User
{
    return userWith(['loan-applications.view', 'loan-applications.manage', 'collaterals.view', 'collaterals.manage'], 'AO Test');
}

it('reports a missing Codex configuration instead of guessing', function () {
    config(['services.codex.endpoint' => null]);
    $this->actingAs(loanOfficer());

    $this->getJson(route('loan-applications.lookup', ['nik' => '3201000000000001']))->assertStatus(503);
});

it('looks applicants up in Codex when configured, refreshing the token once on 401', function () {
    config(['services.codex.endpoint' => 'https://codex.test', 'services.codex.id' => 'id', 'services.codex.secret' => 'secret']);
    Http::fake([
        'codex.test/oauth/token' => Http::response(['access_token' => 'tok', 'expires_in' => 3600]),
        'codex.test/api/customers/3201000000000001' => Http::sequence()->push([], 401)->push(['data' => ['nomor_ktp' => '3201000000000001', 'nama_lengkap' => 'Siti', 'jenis_kelamin' => 'P', 'nomor_cif' => 'C1']]),
        'codex.test/api/customers/3201000000000002' => Http::response([], 404),
        'codex.test/api/customers/3201000000000003' => Http::response([], 500),
    ]);
    $this->actingAs(loanOfficer());

    $this->getJson(route('loan-applications.lookup', ['nik' => '3201000000000001']))->assertJsonPath('customer.full_name', 'Siti')->assertJsonPath('customer.gender', 'FEMALE');
    $this->getJson(route('loan-applications.lookup', ['nik' => '3201000000000002']))->assertJsonPath('found', false);
    $this->getJson(route('loan-applications.lookup', ['nik' => '3201000000000003']))->assertStatus(503);
});

it('opens a draft only for registered applicants', function () {
    fakeCustomers();
    $officer = loanOfficer();
    $this->actingAs($officer);

    $this->post(route('loan-applications.store'), ['nik' => '123'])->assertSessionHasErrors('nik');
    $this->post(route('loan-applications.store'), ['nik' => '1111111111111111'])->assertSessionHasErrors('nik');
    $this->post(route('loan-applications.store'), ['nik' => '3201000000000001'])->assertSessionHasNoErrors();

    $loan = LoanApplication::firstOrFail();
    expect($loan->application_code)->toBe('00700001')->and($loan->status)->toBe(LoanStatus::Draft)->and($loan->created_by)->toBe($officer->id);
});

it('lists only the current user\'s applications', function () {
    $mine = loanOfficer();
    $theirs = userWith(['loan-applications.view'], 'Other');
    LoanApplication::create(['application_code' => '00700001', 'application_date' => now(), 'status' => 'draft', 'nik' => '1', 'full_name' => 'Mine', 'created_by' => $mine->id]);
    LoanApplication::create(['application_code' => '00700002', 'application_date' => now(), 'status' => 'draft', 'nik' => '2', 'full_name' => 'Theirs', 'created_by' => $theirs->id]);

    $this->actingAs($mine)->get(route('loan-applications.index'))
        ->assertInertia(fn (Assert $page) => $page->component('loan-applications/index')->has('loans.data', 1)->where('loans.data.0.full_name', 'Mine'));
});

it('enforces product limits on the application data', function () {
    $s = loanSetup();
    $this->actingAs($officer = loanOfficer());
    $loan = LoanApplication::create(['application_code' => '00700001', 'application_date' => now(), 'status' => 'draft', 'nik' => '1', 'full_name' => 'A', 'created_by' => $officer->id]);

    $this->put(route('loan-applications.update', $loan), loanPayload($s, ['requested_amount' => 60_000_000]))->assertSessionHasErrors(['requested_amount' => 'The maximum loan amount is Rp50.000.000 for this product.']);
    $this->put(route('loan-applications.update', $loan), loanPayload($s, ['requested_amount' => 500]))->assertSessionHasErrors('requested_amount');
    $this->put(route('loan-applications.update', $loan), loanPayload($s, ['requested_tenor' => 36]))->assertSessionHasErrors('requested_tenor');
    $this->put(route('loan-applications.update', $loan), loanPayload($s, ['method_id' => $s['otherMethod']->id]))->assertSessionHasErrors('method_id');
    $this->put(route('loan-applications.update', $loan), loanPayload($s, ['supervisor_id' => User::factory()->create()->id]))->assertSessionHasErrors('supervisor_id');

    $foreignPath = CommitteePath::create(['product_id' => Product::create(['code' => '02', 'alias' => 'KUP', 'name' => 'X', 'is_active' => true])->id, 'mechanism' => 'plafon']);
    $this->put(route('loan-applications.update', $loan), loanPayload($s, ['committee_path_id' => $foreignPath->id]))->assertSessionHasErrors('committee_path_id');

    $this->put(route('loan-applications.update', $loan), loanPayload($s))->assertSessionHasNoErrors();
    expect($loan->fresh()->only(['requested_amount', 'marketing']))->toBe(['requested_amount' => 10_000_000, 'marketing' => 'ADI']);
});

it('only lets the creator change a draft', function () {
    $s = loanSetup();
    $owner = loanOfficer();
    $loan = LoanApplication::create(['application_code' => '00700001', 'application_date' => now(), 'status' => 'draft', 'nik' => '1', 'full_name' => 'A', 'created_by' => $owner->id]);

    $this->actingAs(loanOfficer())->put(route('loan-applications.update', $loan), loanPayload($s))->assertForbidden();
    $this->actingAs(loanOfficer())->delete(route('loan-applications.destroy', $loan))->assertForbidden();

    $loan->update(['status' => LoanStatus::Submitted]);
    $this->actingAs($owner)->put(route('loan-applications.update', $loan), loanPayload($s))->assertForbidden();
    $this->actingAs($owner)->delete(route('loan-applications.destroy', $loan))->assertForbidden();
});

it('submits a complete draft, notifies schedulers and locks the file', function () {
    $s = loanSetup(collateralRequired: true);
    $scheduler = userWith(['scheduling.manage', 'scheduling.view'], 'Scheduler');
    $this->actingAs($officer = loanOfficer());
    fakeCustomers();
    $this->post(route('loan-applications.store'), ['nik' => '3201000000000001']);
    $loan = LoanApplication::firstOrFail();

    $this->post(route('loan-applications.confirm', $loan))->assertSessionHas('error');

    $this->put(route('loan-applications.update', $loan), loanPayload($s));
    $this->post(route('loan-applications.confirm', $loan))->assertSessionHas('error'); // collateral required

    CollateralType::create(['code' => '05', 'name' => 'LAND']);
    Region::create(['code' => '3201', 'regency' => 'BOGOR', 'district' => 'X', 'village' => 'Y']);
    $this->post(route('loan-applications.collaterals.store', $loan), [
        'collateral_type_code' => '05', 'document_number' => 'shm 1', 'owner_name' => 'budi', 'owner_address' => 'jl x', 'region_code' => '3201', 'description' => 'house',
    ])->assertSessionHasNoErrors();
    expect($loan->collaterals()->count())->toBe(1)->and(Collateral::first()->owner_name)->toBe('BUDI');

    $this->post(route('loan-applications.confirm', $loan))->assertSessionHas('success');
    expect($loan->fresh()->status)->toBe(LoanStatus::Submitted)
        ->and(AppNotification::where('user_id', $scheduler->id)->count())->toBe(1);

    $this->delete(route('loan-applications.collaterals.detach', [$loan, Collateral::first()]))->assertForbidden();
});

it('attaches and detaches collateral and refuses to delete attached collateral', function () {
    loanSetup();
    $this->actingAs($officer = loanOfficer());
    $loan = LoanApplication::create(['application_code' => '00700001', 'application_date' => now(), 'status' => 'draft', 'nik' => '1', 'full_name' => 'A', 'created_by' => $officer->id]);
    $collateral = Collateral::create(['collateral_type_code' => '05', 'owner_name' => 'X']);

    $this->post(route('loan-applications.collaterals.attach', $loan), ['collateral_id' => $collateral->id])->assertSessionHasNoErrors();
    $this->post(route('loan-applications.collaterals.attach', $loan), ['collateral_id' => 999])->assertSessionHasErrors('collateral_id');
    $this->delete(route('collaterals.destroy', $collateral))->assertSessionHas('error');

    $this->delete(route('loan-applications.collaterals.detach', [$loan, $collateral]))->assertSessionHas('success');
    $this->delete(route('collaterals.destroy', $collateral))->assertSessionHas('success');
    expect(Collateral::count())->toBe(0);
});

it('refuses to delete master data that loan applications use', function () {
    $s = loanSetup();
    $this->actingAs(superAdmin());
    LoanApplication::create(['application_code' => '00700001', 'application_date' => now(), 'status' => 'draft', 'nik' => '1', 'full_name' => 'A', 'product_id' => $s['product']->id, 'office_id' => $s['office']->id]);

    $this->delete(route('references.destroy', ['products', $s['product']->id]))->assertSessionHas('error');
    $this->delete(route('references.destroy', ['offices', $s['office']->id]))->assertSessionHas('error');
});

it('describes attachable collaterals fully so similar ones cannot be confused', function () {
    $s = loanSetup();
    CollateralType::create(['code' => '04', 'name' => 'SKMHT : SURAT KUASA MEMBEBANKAN HAK TANGGUNGAN']);
    $this->actingAs($officer = loanOfficer());
    $loan = LoanApplication::create(['application_code' => '00700001', 'application_date' => now(), 'status' => 'draft', 'nik' => '1', 'full_name' => 'A', 'created_by' => $officer->id]);
    Collateral::create([
        'collateral_type_code' => '04', 'owner_name' => 'ONIH', 'document_number' => 'SHM 1234/2020', 'appraisal_value' => 150_000_000,
        'description' => 'DUSUN WANARASA RT 20 RW 05 DESA SUKAMANDI KECAMATAN PAGADEN BARAT',
    ]);

    $this->get(route('loan-applications.show', $loan))->assertInertia(fn (Assert $page) => $page
        ->where('collateralOptions.0.label', '#1 — ONIH')
        ->where('collateralOptions.0.description', 'Doc SHM 1234/2020 · SKMHT : SURAT KUASA MEMBEBANKAN HAK TANGGUNGAN · Appraisal Rp150.000.000 · DUSUN WANARASA RT 20 RW 05 DESA SUKAMANDI KECAMATAN PAGADEN BARAT'));
});
