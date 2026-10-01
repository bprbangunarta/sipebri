<?php

use App\Enums\LoanStatus;
use App\Models\AnalysisBusiness;
use App\Models\AppNotification;
use App\Models\Collateral;
use App\Models\LoanAnalysis;
use App\Models\LoanApplication;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\CommitteeSeeder;
use Database\Seeders\RoleSeeder;
use Inertia\Testing\AssertableInertia as Assert;

/** @return array{analyst: User, kasi: User, loan: LoanApplication} */
function analysisSetup(): array
{
    test()->seed(RoleSeeder::class);
    $analyst = User::factory()->create()->assignRole('Staff Analis & Appraisal');
    $kasi = User::factory()->create()->assignRole('Kepala Seksi Analis');
    $product = Product::firstOrCreate(['alias' => 'KRU'], ['code' => 'KRU', 'name' => 'KRU', 'is_active' => true]);
    test()->seed(CommitteeSeeder::class);

    $loan = LoanApplication::create([
        'application_code' => LoanApplication::nextCode(), 'application_date' => now(), 'status' => LoanStatus::Survey,
        'nik' => '3201000000000001', 'full_name' => 'Siti', 'product_id' => $product->id, 'requested_amount' => 12_000_000, 'requested_tenor' => 12,
        'surveyor_id' => $analyst->id, 'supervisor_id' => $kasi->id,
    ]);

    return compact('analyst', 'kasi', 'loan');
}

it('lists the files of the signed-in analyst, surveyed or already in analysis', function () {
    ['analyst' => $analyst, 'loan' => $loan] = analysisSetup();
    $other = LoanApplication::create([
        'application_code' => LoanApplication::nextCode(), 'application_date' => now(), 'status' => LoanStatus::Analysis,
        'nik' => '3201000000000002', 'full_name' => 'Ani', 'surveyor_id' => $analyst->id,
    ]);

    $this->actingAs($analyst)->get(route('credit-analysis.index'))
        ->assertInertia(fn (Assert $page) => $page->component('credit-analysis/index')->has('loans.data', 2)->where('loans.data.0.id', $other->id));
});

it('opens the worksheet for the surveyor and the section head only', function () {
    ['analyst' => $analyst, 'kasi' => $kasi, 'loan' => $loan] = analysisSetup();

    $this->actingAs($analyst)->get(route('credit-analysis.show', $loan))->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('credit-analysis/show')->where('canEdit', true)->has('sections', 8)->where('submission.gaps.0', 'Analisa Usaha (minimal satu usaha)'));

    $this->actingAs($kasi)->get(route('credit-analysis.show', $loan))->assertOk()->assertInertia(fn (Assert $page) => $page->where('canEdit', false));

    $stranger = User::factory()->create()->assignRole('Staff Analis & Appraisal');
    $this->actingAs($stranger)->get(route('credit-analysis.show', $loan))->assertForbidden();
    $this->actingAs(userWith(['dashboard.view']))->get(route('credit-analysis.show', $loan))->assertForbidden();
});

it('moves the file to the analysis stage at the first save and refuses a section head or a stranger to change it', function () {
    ['analyst' => $analyst, 'kasi' => $kasi, 'loan' => $loan] = analysisSetup();

    $this->actingAs($kasi)->put(route('credit-analysis.memorandum.update', $loan), ['proposed_amount' => 1])->assertForbidden();
    expect($loan->fresh()->status)->toBe(LoanStatus::Survey)->and(LoanAnalysis::count())->toBe(0);

    $this->actingAs($analyst)->put(route('credit-analysis.memorandum.update', $loan), ['proposed_amount' => 10_000_000, 'term_months' => 12, 'interest_rate' => '1.5', 'binding' => 'FIDUCIA', 'working_capital' => 5_000_000, 'working_capital_note' => 'stock'])
        ->assertSessionHasNoErrors();

    $memorandum = $loan->fresh()->analysis->memorandum;
    expect($loan->fresh()->status)->toBe(LoanStatus::Analysis)
        ->and($memorandum->proposed_amount)->toBe(10_000_000)->and($memorandum->interest_rate)->toBe(1.5)->and($memorandum->working_capital_note)->toBe('STOCK');

    $this->actingAs($analyst)->put(route('credit-analysis.memorandum.update', $loan), ['binding' => 'NOT A BINDING'])->assertSessionHasErrors('binding');
});

it('saves the administration fees, the 5C and the qualitative answers', function () {
    ['analyst' => $analyst, 'loan' => $loan] = analysisSetup();
    $this->actingAs($analyst);

    $this->put(route('credit-analysis.administration.update', $loan), ['administration' => 100_000, 'fiducia_fee' => 50_000])->assertSessionHasNoErrors();
    expect($loan->fresh()->analysis->administration->total())->toBe(150_000);

    $this->put(route('credit-analysis.five-c.update', $loan), ['lifestyle' => 3, 'capital_source' => 4])->assertSessionHasErrors('capital_source');
    $this->put(route('credit-analysis.five-c.update', $loan), ['lifestyle' => 3, 'capital_source' => 2])->assertSessionHasNoErrors();
    expect($loan->fresh()->analysis->fiveC->metrics()['percent'])->toBe(83.34);

    $this->put(route('credit-analysis.qualitative.update', $loan), ['slik_check' => 4, 'neighbor_relations' => 'BAIK', 'strength' => 'location'])->assertSessionHasNoErrors();
    $this->put(route('credit-analysis.qualitative.update', $loan), ['neighbor_relations' => 'TERRIBLE'])->assertSessionHasErrors('neighbor_relations');
    expect($loan->fresh()->analysis->qualitative->strength)->toBe('LOCATION');
});

it('saves household costs with obligations and ownership, and does not rewrite rows that did not change', function () {
    ['analyst' => $analyst, 'loan' => $loan] = analysisSetup();
    $this->actingAs($analyst);

    $payload = ['cost_staple' => 2_000_000, 'items' => [['name' => 'bank a', 'amount' => 1_000_000]]];
    $this->put(route('credit-analysis.finance.update', $loan), $payload)->assertSessionHasNoErrors();

    $finance = $loan->fresh()->analysis->finance;
    $firstId = $finance->items->first()->id;
    expect($finance->items->first()->name)->toBe('BANK A')->and($finance->metrics()['obligation_cost'])->toBe(1_000_000);

    $this->put(route('credit-analysis.finance.update', $loan), $payload)->assertSessionHasNoErrors();
    expect($finance->fresh()->items()->first()->id)->toBe($firstId);

    $this->put(route('credit-analysis.ownership.update', $loan), ['asset_house' => 'PERMANEN', 'asset_car' => '9 UNIT'])->assertSessionHasErrors('asset_car');
    $this->put(route('credit-analysis.ownership.update', $loan), ['asset_house' => 'PERMANEN', 'items' => [['name' => 'land']]])->assertSessionHasNoErrors();
    expect($finance->fresh()->asset_house)->toBe('PERMANEN')->and($finance->fresh()->items->where('group', 'asset')->count())->toBe(1)
        ->and($finance->fresh()->items->where('group', 'obligation')->count())->toBe(1);
});

it('records a check of each collateral of the file and refuses one that is not the file’s', function () {
    ['analyst' => $analyst, 'loan' => $loan] = analysisSetup();
    $collateral = Collateral::create(['collateral_type_code' => '05', 'appraisal_value' => 8_000_000]);
    $loan->collaterals()->attach($collateral);
    $this->actingAs($analyst);

    $this->get(route('credit-analysis.show', $loan))->assertInertia(fn (Assert $page) => $page->where('collaterals.0.appraisal_value', 8_000_000)->where('memorandum.appraisal_total', 8_000_000));

    $this->put(route('credit-analysis.collaterals.update', $loan), ['rows' => [['collateral_id' => $collateral->id + 99, 'kind' => 'land']]])->assertSessionHasErrors('rows.0.collateral_id');
    $this->put(route('credit-analysis.collaterals.update', $loan), ['rows' => [['collateral_id' => $collateral->id, 'kind' => 'vehicle', 'brand' => 'honda', 'year' => '2020', 'appraisal_value' => 7_500_000]]])->assertSessionHasNoErrors();

    $check = $loan->fresh()->analysis->collateralChecks->first();
    expect($check->brand)->toBe('HONDA')->and($check->appraisal_value)->toBe(7_500_000)->and($check->kind)->toBe('vehicle');
});

it('creates, changes and deletes a business, working out its figures', function () {
    ['analyst' => $analyst, 'loan' => $loan] = analysisSetup();
    $this->actingAs($analyst);

    $this->post(route('credit-analysis.businesses.store', $loan), ['type' => 'service', 'name' => 'ojek'])->assertRedirect();
    $business = AnalysisBusiness::firstOrFail();
    expect($business->code)->toBe('AUJ00001')->and($business->name)->toBe('OJEK');

    $this->get(route('credit-analysis.businesses.show', [$loan, $business]))->assertOk()->assertInertia(fn (Assert $page) => $page->component('credit-analysis/business')->where('business.code', 'AUJ00001'));

    $this->put(route('credit-analysis.businesses.update', [$loan, $business]), ['service_income' => 3_000_000, 'vehicle_tax' => 200_000, 'other_expense' => 300_000])->assertSessionHasNoErrors();
    expect($business->fresh()->net_profit)->toBe(2_500_000)->and($business->fresh()->monthly_income)->toBe(2_500_000);

    $this->put(route('credit-analysis.businesses.update', [$loan, $business]), ['business_length' => '99 TAHUN'])->assertSessionHasErrors('business_length');

    $this->delete(route('credit-analysis.businesses.destroy', [$loan, $business]))->assertRedirect(route('credit-analysis.show', $loan));
    expect(AnalysisBusiness::count())->toBe(0);
});

it('keeps the businesses of one file from another', function () {
    ['analyst' => $analyst, 'loan' => $loan] = analysisSetup();
    $this->actingAs($analyst);
    $this->post(route('credit-analysis.businesses.store', $loan), ['type' => 'trade', 'name' => 'shop']);
    $business = AnalysisBusiness::firstOrFail();

    $other = LoanApplication::create([
        'application_code' => LoanApplication::nextCode(), 'application_date' => now(), 'status' => LoanStatus::Survey,
        'nik' => '3201000000000009', 'full_name' => 'Ani', 'surveyor_id' => $analyst->id,
    ]);

    $this->get(route('credit-analysis.businesses.show', [$other, $business]))->assertNotFound();
    $this->put(route('credit-analysis.businesses.update', [$other, $business]), ['name' => 'x'])->assertNotFound();
});

it('sends the file to the committee only when the required parts are filled in', function () {
    ['analyst' => $analyst, 'kasi' => $kasi, 'loan' => $loan] = analysisSetup();
    $this->actingAs($analyst);

    $this->post(route('credit-analysis.submit', $loan))->assertSessionHas('error');
    expect($loan->fresh()->status)->toBe(LoanStatus::Analysis);

    $this->post(route('credit-analysis.businesses.store', $loan), ['type' => 'service', 'name' => 'ojek']);
    $this->put(route('credit-analysis.finance.update', $loan), ['cost_staple' => 1_000_000]);
    $this->put(route('credit-analysis.five-c.update', $loan), ['capital_source' => 3]);
    $this->put(route('credit-analysis.memorandum.update', $loan), ['proposed_amount' => 10_000_000]);

    $this->post(route('credit-analysis.submit', $loan))->assertRedirect(route('credit-analysis.index'));

    expect($loan->fresh()->status)->toBe(LoanStatus::Committee)->and($loan->fresh()->analysis->submitted_by)->toBe($analyst->id)
        ->and(AppNotification::where('user_id', $kasi->id)->where('title', 'Berkas siap diputus komite')->exists())->toBeTrue();

    // Once at the committee the worksheet is read-only.
    $this->put(route('credit-analysis.memorandum.update', $loan), ['proposed_amount' => 1])->assertForbidden();
});
