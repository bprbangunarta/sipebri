<?php

use App\Models\CollateralType;
use App\Models\Installment;
use App\Models\Institution;
use App\Models\LoanApplication;
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
    'institution' => ['resorts', Institution::class, ['code' => 'i1', 'name' => 'Alpha'], ['code' => 'I2', 'name' => 'Beta']],
    'method' => ['interest-methods', Method::class, ['code' => 'a1', 'name' => 'Alpha'], ['code' => 'A2', 'name' => 'Beta']],
    'installment' => ['installments', Installment::class, ['code' => 'x', 'name' => 'Alpha', 'period_months' => 3], ['code' => 'X', 'name' => 'Beta', 'period_months' => 0]],
    'product' => ['products', Product::class, ['code' => 'p1', 'alias' => 'pa', 'name' => 'Alpha', 'is_active' => true], ['code' => 'P1', 'alias' => 'PB', 'name' => 'Beta', 'is_active' => false]],
    'region' => ['regions', Region::class, ['code' => '3201', 'regency' => 'a', 'district' => 'b', 'village' => 'c'], ['code' => '3201', 'regency' => 'A', 'district' => 'B', 'village' => 'D']],
]);

it('creates, updates and deletes a record', function (string $slug, string $model, array $create, array $update) {
    $this->post(route('references.store', $slug), $create)->assertSessionHasNoErrors();
    $record = $model::query()->latest('id')->first();
    expect($record)->not->toBeNull();

    $this->put(route('references.update', [$slug, $record->id]), $update)->assertSessionHasNoErrors();
    expect($model::count())->toBe(1);

    $this->delete(route('references.destroy', [$slug, $record->id]))->assertSessionHas('success');
    expect($model::count())->toBe(0);
})->with('usable');

it('uppercases coded values and trims input', function () {
    $this->post(route('references.store', 'products'), ['code' => ' ab ', 'alias' => 'x', 'name' => ' loan ', 'is_active' => true]);

    expect(Product::first()->only(['code', 'alias', 'name']))->toBe(['code' => 'AB', 'alias' => 'X', 'name' => 'LOAN']);
});

it('validates required and unique values', function () {
    Method::create(['code' => 'M1', 'name' => 'First']);
    $other = Method::create(['code' => 'M2', 'name' => 'Second']);

    $this->post(route('references.store', 'interest-methods'), ['code' => '', 'name' => 'X'])->assertSessionHasErrors('code');
    $this->post(route('references.store', 'interest-methods'), ['code' => 'm1', 'name' => 'Dup'])->assertSessionHasErrors('code');
    $this->put(route('references.update', ['interest-methods', $other->id]), ['code' => 'M1', 'name' => 'Second'])->assertSessionHasErrors('code');
    $this->put(route('references.update', ['interest-methods', $other->id]), ['code' => 'M2', 'name' => 'Renamed'])->assertSessionHasNoErrors();
});

it('scopes uniqueness of ownership codes to the collateral type', function () {
    CollateralType::create(['code' => '05', 'name' => 'Land']);
    CollateralType::create(['code' => '06', 'name' => 'Other']);

    $this->post(route('references.store', 'collateral-classifications'), ['collateral_type_code' => '05', 'code' => '01', 'name' => 'A'])->assertSessionHasNoErrors();
    $this->post(route('references.store', 'collateral-classifications'), ['collateral_type_code' => '05', 'code' => '01', 'name' => 'B'])->assertSessionHasErrors('code');
    $this->post(route('references.store', 'collateral-classifications'), ['collateral_type_code' => '06', 'code' => '01', 'name' => 'B'])->assertSessionHasNoErrors();
    $this->post(route('references.store', 'collateral-classifications'), ['collateral_type_code' => '77', 'code' => '02', 'name' => 'C'])->assertSessionHasErrors('collateral_type_code');
    expect(OwnershipStatus::count())->toBe(2);
});

it('searches, paginates and reports usage', function () {
    foreach (range(1, 30) as $i) {
        Institution::create(['code' => sprintf('%03d', $i), 'name' => sprintf('Institution %02d', $i)]);
    }

    $this->get(route('references.index', 'resorts'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('references/index')
            ->has('items.data', 25)
            ->where('items.total', 30)
            ->where('items.data.0.usage_count', 0)
            ->where('resource.tracks_usage', true));

    $this->get(route('references.index', ['resorts', 'search' => 'Institution 3']))
        ->assertInertia(fn (Assert $page) => $page->has('items.data', 1));
});

it('rejects unknown resources and everybody who is not Super Admin', function () {
    $this->get('/references/unknown')->assertNotFound();

    $this->actingAs(userWith(['dashboard.view', 'loan-applications.view', 'loan-applications.manage', 'collaterals.manage']));
    $this->get(route('references.index', 'regions'))->assertForbidden();
    $this->post(route('references.store', 'regions'), ['code' => 'X'])->assertForbidden();
    $this->put(route('products.parameters.update', Product::create(['code' => '09', 'alias' => 'XX', 'name' => 'X', 'is_active' => true])), ['max_amount' => 1])->assertForbidden();
});

it('saves product parameters and validates their consistency', function () {
    $product = Product::create(['code' => '01', 'alias' => 'KRU', 'name' => 'Loan', 'is_active' => true]);
    $allowed = Method::create(['code' => '10', 'name' => 'Flat']);
    $other = Method::create(['code' => '20', 'name' => 'Effective']);

    $this->get(route('products.parameters', $product))->assertInertia(fn (Assert $page) => $page->component('references/product-parameters'));

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

dataset('sort keys', function () {
    // Datasets are built before the application boots, so the registry is read from its file.
    foreach (require __DIR__.'/../../config/references.php' as $slug => $definition) {
        foreach ([...array_column($definition['fields'], 'name'), ...($definition['usage'] === [] ? [] : ['usage_count'])] as $key) {
            foreach (['asc', 'desc'] as $direction) {
                yield "$slug $key $direction" => [$slug, $key, $direction];
            }
        }
    }
});

it('sorts every reference list by every column in both directions without error', function (string $slug, string $key, string $direction) {
    $this->get(route('references.index', [$slug, 'sort' => $key, 'direction' => $direction]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->where('filters.sort', $key)->where('filters.direction', $direction));
})->with('sort keys');

it('puts the rows in the order of the sorted column, ties by the first column', function (string $key, string $direction) {
    Institution::create(['code' => 'A1', 'name' => 'BRAVO']);
    Institution::create(['code' => 'A2', 'name' => 'ALPHA']);
    Institution::create(['code' => 'A3', 'name' => 'CHARLIE']);
    Institution::create(['code' => 'A4', 'name' => 'ALPHA']);
    $expected = $direction === 'asc' ? ['A2', 'A4', 'A1', 'A3'] : ['A3', 'A1', 'A2', 'A4'];

    if ($key === 'code') {
        $expected = $direction === 'asc' ? ['A1', 'A2', 'A3', 'A4'] : ['A4', 'A3', 'A2', 'A1'];
    }

    $this->get(route('references.index', ['resorts', 'sort' => $key, 'direction' => $direction]))
        ->assertInertia(fn (Assert $page) => $page->where('items.data', fn ($rows) => collect($rows)->pluck('code')->all() === $expected));
})->with([['name', 'asc'], ['name', 'desc'], ['code', 'asc'], ['code', 'desc']]);

it('sorts by how many records use a row, as the Dipakai column shows', function () {
    $used = Institution::create(['code' => 'U1', 'name' => 'USED']);
    Institution::create(['code' => 'U0', 'name' => 'FREE']);
    LoanApplication::create(['application_code' => '00700001', 'application_date' => now(), 'status' => 'draft', 'nik' => '1', 'full_name' => 'A', 'created_by' => auth()->id(), 'institution_id' => $used->id]);

    $order = fn (string $direction): array => collect($this->get(route('references.index', ['resorts', 'sort' => 'usage_count', 'direction' => $direction]))->inertiaProps('items.data'))->pluck('code')->all();

    expect($order('desc'))->toBe(['U1', 'U0'])->and($order('asc'))->toBe(['U0', 'U1']);
});

it('refuses a sort key that is not a column of the table', function (string $key) {
    $this->get(route('references.index', ['resorts', 'sort' => $key]))->assertSessionHasErrors('sort');
})->with(['id', 'password', 'usage_count; drop table users', 'regency']);

it('refuses the usage sort on a table that tracks no usage', function () {
    $this->get(route('references.index', ['regions', 'sort' => 'usage_count']))->assertSessionHasErrors('sort');
});

it('shows in the Dipakai column how many records use a row, with relations of two or more words', function () {
    $resort = Institution::create(['code' => 'U1', 'name' => 'USED']);
    LoanApplication::create(['application_code' => '00700001', 'application_date' => now(), 'status' => 'draft', 'nik' => '1', 'full_name' => 'A', 'created_by' => auth()->id(), 'institution_id' => $resort->id]);

    $this->get(route('references.index', 'resorts'))->assertInertia(fn (Assert $page) => $page->where('items.data.0.usage_count', 1));
});
