<?php

use App\Models\BindingType;
use App\Models\Collateral;
use App\Models\CollateralCondition;
use App\Models\CollateralType;
use App\Models\Region;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->actingAs(superAdmin());
    CollateralType::create(['code' => '05', 'name' => 'LAND']);
    BindingType::create(['code' => '01', 'name' => 'APHT']);
    CollateralCondition::create(['code' => '9', 'name' => 'OK']);
    Region::create(['code' => '3201', 'regency' => 'BOGOR', 'district' => 'X', 'village' => 'Y']);
});

function collateralPayload(array $overrides = []): array
{
    return array_merge([
        'cbs_id' => 'ag-1', 'collateral_type_code' => '05', 'binding_type_code' => '01', 'document_number' => 'shm 1', 'description' => 'house',
        'owner_name' => 'budi', 'owner_address' => 'jl x', 'region_code' => '3201', 'appraisal_value' => 25_000_000,
    ], $overrides);
}

it('creates a collateral with uppercase text and default codes', function () {
    $this->post(route('collaterals.store'), collateralPayload())->assertSessionHasNoErrors();

    $c = Collateral::firstOrFail();
    expect($c->only(['cbs_id', 'owner_name', 'insurance_code', 'ppap_code', 'appraisal_value']))->toBe(['cbs_id' => 'AG-1', 'owner_name' => 'BUDI', 'insurance_code' => 'T', 'ppap_code' => '1', 'appraisal_value' => 25_000_000]);
});

it('validates references and unique collateral ids', function () {
    $this->post(route('collaterals.store'), collateralPayload());

    $this->post(route('collaterals.store'), collateralPayload(['cbs_id' => 'AG-1']))->assertSessionHasErrors('cbs_id');
    $this->post(route('collaterals.store'), collateralPayload(['cbs_id' => 'x', 'collateral_type_code' => '77']))->assertSessionHasErrors('collateral_type_code');
    $this->post(route('collaterals.store'), collateralPayload(['cbs_id' => 'y', 'region_code' => '0000']))->assertSessionHasErrors('region_code');
    $this->post(route('collaterals.store'), collateralPayload(['cbs_id' => 'z', 'owner_name' => '']))->assertSessionHasErrors('owner_name');
});

it('requires condition, insurance and dates when editing and records the appraiser', function () {
    $this->post(route('collaterals.store'), collateralPayload());
    $c = Collateral::firstOrFail();

    $this->put(route('collaterals.update', $c), collateralPayload())->assertSessionHasErrors(['condition_code', 'condition_date', 'insurance_code', 'insurance_date']);

    $this->put(route('collaterals.update', $c), collateralPayload(['condition_code' => '9', 'condition_date' => '2026-01-01', 'insurance_code' => 'Y', 'insurance_date' => '2026-02-01']))->assertSessionHasNoErrors();
    expect($c->fresh()->appraised_at->toDateString())->toBe(now()->toDateString())->and($c->fresh()->appraiser_name)->not->toBeNull();
});

it('searches and filters the list and maps the CBS payload', function () {
    $this->post(route('collaterals.store'), collateralPayload());
    $this->post(route('collaterals.store'), collateralPayload(['cbs_id' => 'ag-2', 'owner_name' => 'siti']));

    $this->get(route('collaterals.index', ['search' => 'SITI']))->assertInertia(fn (Assert $page) => $page->has('collaterals.data', 1));
    $this->get(route('collaterals.index', ['type' => '77']))->assertInertia(fn (Assert $page) => $page->has('collaterals.data', 0));
    expect(Collateral::first()->toCbsPayload())->toMatchArray(['jenis' => '05', 'lokasi' => '3201'])->and(Collateral::first()->toCbsPayload()['nilai']['taksasi'])->toBe(25_000_000);
});

it('restricts collaterals to permitted users', function () {
    $this->actingAs(userWith(['collaterals.view']));
    $this->get(route('collaterals.index'))->assertOk();
    $this->get(route('collaterals.create'))->assertForbidden();
});

it('orders the collaterals by every column of the list, both ways', function (string $key) {
    CollateralType::create(['code' => '06', 'name' => 'VEHICLE']);
    $this->travelTo(now()->subDay());
    $a = Collateral::create(['cbs_id' => 'AG-1', 'collateral_type_code' => '05', 'owner_name' => 'ANI', 'description' => 'A HOUSE', 'document_number' => 'DOC 1', 'appraisal_value' => 1_000_000]);
    $this->travelBack();
    $b = Collateral::create(['cbs_id' => 'AG-2', 'collateral_type_code' => '06', 'owner_name' => 'BUDI', 'description' => 'B LAND', 'document_number' => 'DOC 2', 'appraisal_value' => 2_000_000]);

    foreach (['asc' => [$a->id, $b->id], 'desc' => [$b->id, $a->id]] as $direction => [$top, $bottom]) {
        $this->get(route('collaterals.index', ['sort' => $key, 'direction' => $direction]))->assertInertia(fn (Assert $page) => $page
            ->where('filters.sort', $key)->where('filters.direction', $direction)
            ->where('collaterals.data.0.id', $top)->where('collaterals.data.1.id', $bottom));
    }
})->with(['cbs_id', 'collateral_type_code', 'owner_name', 'description', 'document_number', 'appraisal_value', 'created_at']);

it('falls back to the newest collaterals first for an unknown sort key', function () {
    $this->get(route('collaterals.index', ['sort' => 'owner_address']))->assertInertia(fn (Assert $page) => $page->where('filters.sort', 'created_at'));
});
