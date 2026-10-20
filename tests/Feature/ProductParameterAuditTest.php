<?php

use App\Audit\AuditLog;
use App\Enums\LoanStatus;
use App\Models\Installment;
use App\Models\LoanApplication;
use App\Models\Method;
use App\Models\Product;
use App\Models\ProductParameter;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->actingAs(superAdmin());
});

function parameterChoices(): array
{
    return [Method::create(['code' => '10', 'name' => 'FLAT']), Installment::create(['code' => '3', 'name' => 'BULANAN', 'period_months' => 1])];
}

function saveParameter(Product $product, array $input = [], mixed $reason = 'Sesuai SK Direksi baru')
{
    return test()->put(route('products.parameters.update', $product), [...$input, 'reason' => $reason]);
}

function validParameter(array $overrides = []): array
{
    return [...['min_amount' => 1_000_000, 'max_amount' => 50_000_000, 'min_tenor' => 1, 'max_tenor' => 24, 'interest_rate' => 12.5, 'provision_rate' => 1, 'admin_rate' => 0.5, 'rc_threshold' => 70, 'collateral_required' => false], ...$overrides];
}

it('refuses a value that does not fit its field, in one sentence, and saves nothing', function (array $input, string $field) {
    $product = Product::create(['code' => 'X1', 'alias' => 'XPR', 'name' => 'Uji', 'is_active' => true]);
    saveParameter($product, validParameter($input))->assertSessionHasErrors($field);

    expect($product->fresh()->parameter)->toBeNull();
})->with([
    'negative minimum amount' => [['min_amount' => -1], 'min_amount'],
    'zero maximum amount (would read as no limit)' => [['max_amount' => 0], 'max_amount'],
    'fractional amount' => [['max_amount' => 10.5], 'max_amount'],
    'amount as text' => [['max_amount' => 'banyak'], 'max_amount'],
    'tenor zero' => [['min_tenor' => 0], 'min_tenor'],
    'tenor over 600' => [['max_tenor' => 601], 'max_tenor'],
    'maximum tenor under minimum' => [['min_tenor' => 12, 'max_tenor' => 6], 'max_tenor'],
    'rate over 100' => [['interest_rate' => 100.5], 'interest_rate'],
    'negative rate' => [['provision_rate' => -0.1], 'provision_rate'],
    'rate with four decimals' => [['interest_rate' => '12.3456'], 'interest_rate'],
    'rate in scientific notation' => [['admin_rate' => '1e1'], 'admin_rate'],
    'rate as array' => [['interest_rate' => [12]], 'interest_rate'],
    'RC threshold zero (would read as not set)' => [['rc_threshold' => 0], 'rc_threshold'],
    'RC threshold over 100' => [['rc_threshold' => 101], 'rc_threshold'],
    'decree over 100' => [['decree' => str_repeat('x', 101)], 'decree'],
    'note over 255' => [['note' => str_repeat('x', 256)], 'note'],
    'unknown allowed method' => [['allowed_method_ids' => [999]], 'allowed_method_ids.0'],
    'unknown default installment' => [['default_installment_id' => 999], 'default_installment_id'],
    'collateral flag as text' => [['collateral_required' => 'mungkin'], 'collateral_required'],
]);

it('keeps the decimals a rate has, three at most, as they were typed', function () {
    $product = Product::create(['code' => 'X1', 'alias' => 'XPR', 'name' => 'Uji', 'is_active' => true]);
    saveParameter($product, validParameter(['interest_rate' => '12.375', 'provision_rate' => '0', 'admin_rate' => '0.001']))->assertSessionHasNoErrors();

    $p = $product->fresh()->parameter;

    expect([(float) $p->interest_rate, (float) $p->provision_rate, (float) $p->admin_rate])->toBe([12.375, 0.0, 0.001]);
});

it('wants a reason for every change, and keeps it with the audit entry that shows the value before and after', function () {
    $product = Product::create(['code' => 'X1', 'alias' => 'XPR', 'name' => 'Uji', 'is_active' => true]);
    saveParameter($product, validParameter(), null)->assertSessionHasErrors('reason');
    saveParameter($product, validParameter(), 'ya')->assertSessionHasErrors('reason');
    expect($product->fresh()->parameter)->toBeNull();

    saveParameter($product, validParameter())->assertSessionHasNoErrors();
    AuditLog::query()->getQuery()->delete();
    saveParameter($product, validParameter(['max_amount' => 60_000_000]), 'Plafon naik menurut SK 12/2026')->assertSessionHas('success');

    $row = AuditLog::query()->where('event', 'product_parameters.updated')->firstOrFail();

    expect($row->decoded('old_values'))->toBe(['max_amount' => 50_000_000])
        ->and($row->decoded('new_values'))->toBe(['max_amount' => 60_000_000])
        ->and($row->decoded('context'))->toMatchArray(['reason' => 'Plafon naik menurut SK 12/2026']);
});

it('says nothing changed, and writes nothing, when the same values are saved again', function () {
    $product = Product::create(['code' => 'X1', 'alias' => 'XPR', 'name' => 'Uji', 'is_active' => true]);
    saveParameter($product, validParameter())->assertSessionHasNoErrors();
    AuditLog::query()->getQuery()->delete();

    saveParameter($product, validParameter())->assertSessionHas('success', 'Parameter XPR tidak berubah.');

    expect(AuditLog::query()->count())->toBe(0);
});

it('treats a field left out of the request as empty, so a limit can be cleared and nothing is kept by accident', function () {
    $product = Product::create(['code' => 'X1', 'alias' => 'XPR', 'name' => 'Uji', 'is_active' => true]);
    [$method] = parameterChoices();
    saveParameter($product, validParameter(['decree' => 'SK 1/2026', 'allowed_method_ids' => [$method->id]]))->assertSessionHasNoErrors();

    saveParameter($product, ['collateral_required' => false])->assertSessionHasNoErrors();

    $p = $product->fresh()->parameter;

    expect([$p->max_amount, $p->decree, $p->allowed_method_ids, $p->interest_rate])->toBe([null, null, [], null]);
});

it('keeps product parameters for Super Admin only, and for people who are signed in', function () {
    $product = Product::create(['code' => 'X1', 'alias' => 'XPR', 'name' => 'Uji', 'is_active' => true]);
    $this->actingAs(userWith(['dashboard.view', 'loan-applications.manage']));
    $this->get(route('products.parameters', $product))->assertForbidden();
    saveParameter($product, validParameter())->assertForbidden();

    auth()->logout();
    saveParameter($product, validParameter())->assertRedirect(route('login'));

    expect($product->fresh()->parameter)->toBeNull();
});

it('answers not found for a product that does not exist', function () {
    $this->get('/references/products/999/parameters')->assertNotFound();
    $this->put('/references/products/999/parameters', validParameter())->assertNotFound();
});

it('opens the page with the parameter, the methods and the installments', function () {
    $product = Product::create(['code' => 'X1', 'alias' => 'XPR', 'name' => 'Uji', 'is_active' => true]);
    parameterChoices();
    saveParameter($product, validParameter());

    $this->get(route('products.parameters', $product))->assertInertia(fn (Assert $page) => $page
        ->where('parameter.max_amount', 50_000_000)->has('methods', 1)->has('installments', 1));
});

/** A draft saved under the old parameter, then submitted after the parameter changed. */
function draftUnder(array $parameter): array
{
    $s = loanSetup();
    ProductParameter::query()->where('product_id', $s['product']->id)->update($parameter);
    test()->actingAs($officer = loanOfficer());
    fakeCustomers();
    test()->post(route('loan-applications.store'), ['nik' => '3201000000000001']);
    $loan = LoanApplication::firstOrFail();
    test()->put(route('loan-applications.update', $loan), loanPayload($s, ['requested_amount' => 10_000_000, 'requested_tenor' => 12]))->assertSessionHasNoErrors();

    return [$s, $loan, $officer];
}

it('checks a draft against every rule of the parameter again when it is submitted', function (array $change, string $rule) {
    [$s, $loan, $officer] = draftUnder([]);
    $change = array_map(fn ($value) => $value === 'OTHER_METHOD' ? json_encode([$s['otherMethod']->id]) : $value, $change);

    ProductParameter::query()->where('product_id', $s['product']->id)->update($change);

    $this->actingAs($officer)->post(route('loan-applications.confirm', $loan))
        ->assertSessionHas('error', fn (string $message): bool => str_contains(strtolower($message), $rule));
    expect($loan->fresh()->status)->toBe(LoanStatus::Draft);
})->with([
    'a higher minimum amount' => [['min_amount' => 20_000_000], 'plafon'],
    'a lower maximum amount' => [['max_amount' => 5_000_000], 'plafon'],
    'a higher minimum tenor' => [['min_tenor' => 18], 'tenor'],
    'a lower maximum tenor' => [['max_tenor' => 6], 'tenor'],
    'a method that is no longer allowed' => [['allowed_method_ids' => 'OTHER_METHOD'], 'metode'],
    'an installment that is no longer allowed' => [['allowed_installment_ids' => '[999]'], 'angsuran'],
]);

it('submits a draft that still fits the parameter', function () {
    [$s, $loan, $officer] = draftUnder([]);

    $this->actingAs($officer)->post(route('loan-applications.confirm', $loan))->assertSessionHas('success');

    expect($loan->fresh()->status)->toBe(LoanStatus::Submitted);
});
