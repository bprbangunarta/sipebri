<?php

use App\Models\CollateralType;
use App\Models\Installment;
use App\Models\Institution;
use App\Models\Method;
use App\Models\OwnershipStatus;
use App\Models\Product;
use App\Models\ProductParameter;
use App\Models\Region;
use Database\Seeders\CreditReferenceSeeder;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->actingAs(superAdmin());
});

dataset('usable', [
    'institution' => ['institutions', Institution::class, ['code' => 'i1', 'name' => 'Alpha'], ['code' => 'I2', 'name' => 'Beta']],
    'method' => ['methods', Method::class, ['code' => 'a1', 'name' => 'Alpha'], ['code' => 'A2', 'name' => 'Beta']],
    'installment' => ['installments', Installment::class, ['code' => 'x', 'name' => 'Alpha', 'period_months' => 3], ['code' => 'X', 'name' => 'Beta', 'period_months' => 0]],
    'product' => ['products', Product::class, ['code' => 'p1', 'alias' => 'pa', 'name' => 'Alpha', 'is_active' => true], ['code' => 'P1', 'alias' => 'PB', 'name' => 'Beta', 'is_active' => false]],
    'region' => ['regions', Region::class, ['code' => '3201', 'regency' => 'a', 'district' => 'b', 'village' => 'c'], ['code' => '3201', 'regency' => 'A', 'district' => 'B', 'village' => 'D']],
]);

it('creates, updates and deletes a record', function (string $slug, string $model, array $create, array $update) {
    $this->post(route('master-data.store', $slug), $create)->assertSessionHasNoErrors();
    $record = $model::query()->latest('id')->first();
    expect($record)->not->toBeNull();

    $this->put(route('master-data.update', [$slug, $record->id]), $update)->assertSessionHasNoErrors();
    expect($model::count())->toBe(1);

    $this->delete(route('master-data.destroy', [$slug, $record->id]))->assertSessionHas('success');
    expect($model::count())->toBe(0);
})->with('usable');

it('uppercases coded values and trims input', function () {
    $this->post(route('master-data.store', 'products'), ['code' => ' ab ', 'alias' => 'x', 'name' => ' loan ', 'is_active' => true]);

    expect(Product::first()->only(['code', 'alias', 'name']))->toBe(['code' => 'AB', 'alias' => 'X', 'name' => 'LOAN']);
});

it('validates required and unique values', function () {
    Method::create(['code' => 'M1', 'name' => 'First']);
    $other = Method::create(['code' => 'M2', 'name' => 'Second']);

    $this->post(route('master-data.store', 'methods'), ['code' => '', 'name' => 'X'])->assertSessionHasErrors('code');
    $this->post(route('master-data.store', 'methods'), ['code' => 'm1', 'name' => 'Dup'])->assertSessionHasErrors('code');
    $this->put(route('master-data.update', ['methods', $other->id]), ['code' => 'M1', 'name' => 'Second'])->assertSessionHasErrors('code');
    $this->put(route('master-data.update', ['methods', $other->id]), ['code' => 'M2', 'name' => 'Renamed'])->assertSessionHasNoErrors();
});

it('scopes uniqueness of ownership codes to the collateral type', function () {
    CollateralType::create(['code' => '05', 'name' => 'Land']);
    CollateralType::create(['code' => '06', 'name' => 'Other']);

    $this->post(route('master-data.store', 'ownership-statuses'), ['collateral_type_code' => '05', 'code' => '01', 'name' => 'A'])->assertSessionHasNoErrors();
    $this->post(route('master-data.store', 'ownership-statuses'), ['collateral_type_code' => '05', 'code' => '01', 'name' => 'B'])->assertSessionHasErrors('code');
    $this->post(route('master-data.store', 'ownership-statuses'), ['collateral_type_code' => '06', 'code' => '01', 'name' => 'B'])->assertSessionHasNoErrors();
    $this->post(route('master-data.store', 'ownership-statuses'), ['collateral_type_code' => '77', 'code' => '02', 'name' => 'C'])->assertSessionHasErrors('collateral_type_code');
    expect(OwnershipStatus::count())->toBe(2);
});

it('searches, paginates and reports usage', function () {
    foreach (range(1, 30) as $i) {
        Institution::create(['code' => sprintf('%03d', $i), 'name' => sprintf('Institution %02d', $i)]);
    }

    $this->get(route('master-data.index', 'institutions'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('master-data/index')
            ->has('items.data', 25)
            ->where('items.total', 30)
            ->where('items.data.0.usage_count', 0)
            ->where('resource.tracks_usage', true));

    $this->get(route('master-data.index', ['institutions', 'search' => 'Institution 3']))
        ->assertInertia(fn (Assert $page) => $page->has('items.data', 1));
});

it('rejects unknown resources and everybody who is not Super Admin', function () {
    $this->get('/master-data/unknown')->assertNotFound();

    $this->actingAs(userWith(['dashboard.view', 'loan-applications.view', 'loan-applications.manage', 'collaterals.manage']));
    $this->get(route('master-data.index', 'regions'))->assertForbidden();
    $this->post(route('master-data.store', 'regions'), ['code' => 'X'])->assertForbidden();
    $this->put(route('products.parameters.update', Product::create(['code' => '09', 'alias' => 'XX', 'name' => 'X', 'is_active' => true])), ['max_amount' => 1])->assertForbidden();
});

it('saves product parameters and validates their consistency', function () {
    $product = Product::create(['code' => '01', 'alias' => 'KRU', 'name' => 'Loan', 'is_active' => true]);
    $allowed = Method::create(['code' => '10', 'name' => 'Flat']);
    $other = Method::create(['code' => '20', 'name' => 'Effective']);

    $this->get(route('products.parameters', $product))->assertInertia(fn (Assert $page) => $page->component('master-data/product-parameters'));

    $this->put(route('products.parameters.update', $product), ['min_amount' => 5000000, 'max_amount' => 1000000])->assertSessionHasErrors('max_amount');
    $this->put(route('products.parameters.update', $product), ['allowed_method_ids' => [$allowed->id], 'default_method_id' => $other->id])->assertSessionHasErrors('default_method_id');

    $this->put(route('products.parameters.update', $product), [
        'min_amount' => 1000000, 'max_amount' => 5000000, 'min_tenor' => 1, 'max_tenor' => 12, 'interest_rate' => 12.5,
        'allowed_method_ids' => [$allowed->id], 'default_method_id' => $allowed->id, 'collateral_required' => true,
    ])->assertSessionHasNoErrors();

    expect($product->parameter->fresh()->only(['max_amount', 'collateral_required']))->toBe(['max_amount' => 5000000, 'collateral_required' => true]);
});

it('seeds the product parameters of the company staging data and never resets later edits', function () {
    $this->seed(CreditReferenceSeeder::class);

    $kpn = Product::where('code', '12')->firstOrFail()->parameter;
    expect(Product::count())->toBe(17)
        ->and((float) $kpn->interest_rate)->toBe(17.04)
        ->and($kpn->rc_threshold)->toBeNull()
        ->and(ProductParameter::whereNotNull('rc_threshold')->count())->toBe(0);

    $kpn->update(['interest_rate' => 18.5]);
    $this->seed(CreditReferenceSeeder::class);

    expect((float) $kpn->fresh()->interest_rate)->toBe(18.5);
});
