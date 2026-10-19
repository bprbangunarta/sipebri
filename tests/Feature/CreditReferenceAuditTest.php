<?php

use App\Audit\AuditLog;
use App\Models\BindingType;
use App\Models\Collateral;
use App\Models\CollateralCondition;
use App\Models\CollateralMethod;
use App\Models\CollateralType;
use App\Models\CommitteePath;
use App\Models\Installment;
use App\Models\LoanApplication;
use App\Models\LoanApproval;
use App\Models\Method;
use App\Models\OwnershipStatus;
use App\Models\Product;
use App\Models\ProductParameter;
use Illuminate\Support\Arr;

beforeEach(function () {
    $this->actingAs(superAdmin());
});

function loanWith(array $attributes = []): LoanApplication
{
    return LoanApplication::create([
        'application_code' => '0070000'.random_int(1, 9), 'application_date' => now(), 'status' => 'draft', 'nik' => '1', 'full_name' => 'A',
        'created_by' => auth()->id(), ...$attributes,
    ]);
}

function classificationOfNewType(): OwnershipStatus
{
    $type = CollateralType::create(['code' => '05', 'name' => 'LAND']);

    return OwnershipStatus::create(['collateral_type_code' => $type->code, 'code' => '01', 'name' => 'OWN']);
}

/**
 * Each case: the reference table slug, a way to create the row, and the holders that must keep it from being deleted
 * (a closure that attaches one holder to the row, with the noun the refusal should name).
 */
dataset('holders', function () {
    yield 'product by application' => ['products', fn () => Product::create(['code' => 'P1', 'alias' => 'PA', 'name' => 'P', 'is_active' => true]), fn (Product $p) => loanWith(['product_id' => $p->id])];
    yield 'product by committee path' => ['products', fn () => Product::create(['code' => 'P1', 'alias' => 'PA', 'name' => 'P', 'is_active' => true]), fn (Product $p) => CommitteePath::create(['product_id' => $p->id, 'condition' => 'NORMAL', 'mechanism' => 'plafon', 'is_active' => true])];
    yield 'method by application' => ['interest-methods', fn () => Method::create(['code' => 'M1', 'name' => 'FLAT']), fn (Method $m) => loanWith(['method_id' => $m->id])];
    yield 'method by product default' => ['interest-methods', fn () => Method::create(['code' => 'M1', 'name' => 'FLAT']), fn (Method $m) => ProductParameter::create(['product_id' => Product::create(['code' => 'P1', 'alias' => 'PA', 'name' => 'P', 'is_active' => true])->id, 'default_method_id' => $m->id])];
    yield 'method by product allowed list' => ['interest-methods', fn () => Method::create(['code' => 'M1', 'name' => 'FLAT']), fn (Method $m) => ProductParameter::create(['product_id' => Product::create(['code' => 'P1', 'alias' => 'PA', 'name' => 'P', 'is_active' => true])->id, 'allowed_method_ids' => [$m->id]])];
    yield 'method by approval decision' => ['interest-methods', fn () => Method::create(['code' => 'M1', 'name' => 'FLAT']), fn (Method $m) => LoanApproval::create(['loan_application_id' => loanWith()->id, 'sort' => 1, 'role' => 'X', 'method_id' => $m->id])];
    yield 'installment by application' => ['installments', fn () => Installment::create(['code' => 'I1', 'name' => 'MONTHLY', 'period_months' => 1]), fn (Installment $i) => loanWith(['installment_id' => $i->id])];
    yield 'installment by product default' => ['installments', fn () => Installment::create(['code' => 'I1', 'name' => 'MONTHLY', 'period_months' => 1]), fn (Installment $i) => ProductParameter::create(['product_id' => Product::create(['code' => 'P1', 'alias' => 'PA', 'name' => 'P', 'is_active' => true])->id, 'default_installment_id' => $i->id])];
    yield 'installment by product allowed list' => ['installments', fn () => Installment::create(['code' => 'I1', 'name' => 'MONTHLY', 'period_months' => 1]), fn (Installment $i) => ProductParameter::create(['product_id' => Product::create(['code' => 'P1', 'alias' => 'PA', 'name' => 'P', 'is_active' => true])->id, 'allowed_installment_ids' => [$i->id]])];
    yield 'collateral type by collateral' => ['collateral-types', fn () => CollateralType::create(['code' => '05', 'name' => 'LAND']), fn (CollateralType $t) => Collateral::create(['collateral_type_code' => $t->code])];
    yield 'collateral type by classification' => ['collateral-types', fn () => CollateralType::create(['code' => '05', 'name' => 'LAND']), fn (CollateralType $t) => OwnershipStatus::create(['collateral_type_code' => $t->code, 'code' => '01', 'name' => 'OWN'])];
    yield 'binding by collateral' => ['collateral-bindings', fn () => BindingType::create(['code' => '01', 'name' => 'APHT']), fn (BindingType $b) => Collateral::create(['collateral_type_code' => '05', 'binding_type_code' => $b->code])];
    yield 'condition by collateral' => ['collateral-conditions', fn () => CollateralCondition::create(['code' => '9', 'name' => 'OK']), fn (CollateralCondition $c) => Collateral::create(['collateral_type_code' => '05', 'condition_code' => $c->code])];
    yield 'valuation by collateral' => ['collateral-valuations', fn () => CollateralMethod::create(['code' => '1', 'name' => 'LANCAR']), fn (CollateralMethod $m) => Collateral::create(['collateral_type_code' => '05', 'ppap_code' => $m->code])];
});

it('refuses to delete a row that anything still holds, in one sentence, and leaves the holder alone', function (string $slug, Closure $make, Closure $hold) {
    $row = $make();
    $hold($row);

    $this->delete(route('references.destroy', [$slug, $row->id]))->assertSessionHas('error');

    expect($row::query()->count())->toBe(1);
})->with('holders');

it('shows the same holders in the Dipakai column', function (string $slug, Closure $make, Closure $hold) {
    $row = $make();
    $hold($row);

    $this->get(route('references.index', $slug))->assertInertia(fn ($page) => $page->where('items.data.0.usage_count', fn (int $count): bool => $count >= 1));
})->with('holders');

dataset('free rows', [
    'product' => ['products', fn () => Product::create(['code' => 'P1', 'alias' => 'PA', 'name' => 'P', 'is_active' => true])],
    'method' => ['interest-methods', fn () => Method::create(['code' => 'M1', 'name' => 'FLAT'])],
    'installment' => ['installments', fn () => Installment::create(['code' => 'I1', 'name' => 'MONTHLY', 'period_months' => 1])],
    'collateral type' => ['collateral-types', fn () => CollateralType::create(['code' => '05', 'name' => 'LAND'])],
    'binding' => ['collateral-bindings', fn () => BindingType::create(['code' => '01', 'name' => 'APHT'])],
    'condition' => ['collateral-conditions', fn () => CollateralCondition::create(['code' => '9', 'name' => 'OK'])],
    'valuation' => ['collateral-valuations', fn () => CollateralMethod::create(['code' => '1', 'name' => 'LANCAR'])],
    'classification' => ['collateral-classifications', fn () => classificationOfNewType()],
]);

it('deletes a row nothing holds', function (string $slug, Closure $make) {
    $row = $make();

    $this->delete(route('references.destroy', [$slug, $row->id]))->assertSessionHas('success');

    expect($row::query()->count())->toBe(0);
})->with('free rows');

dataset('hostile', [
    'product code over 8' => ['products', ['code' => '123456789', 'alias' => 'PA', 'name' => 'P', 'is_active' => true], 'code'],
    'product alias over 12' => ['products', ['code' => 'P1', 'alias' => str_repeat('A', 13), 'name' => 'P', 'is_active' => true], 'alias'],
    'product name over 100' => ['products', ['code' => 'P1', 'alias' => 'PA', 'name' => str_repeat('x', 101), 'is_active' => true], 'name'],
    'product active as text' => ['products', ['code' => 'P1', 'alias' => 'PA', 'name' => 'P', 'is_active' => 'maybe'], 'is_active'],
    'product code as array' => ['products', ['code' => ['P1'], 'alias' => 'PA', 'name' => 'P', 'is_active' => true], 'code'],
    'installment period negative' => ['installments', ['code' => 'I1', 'name' => 'M', 'period_months' => -1], 'period_months'],
    'installment period over 120' => ['installments', ['code' => 'I1', 'name' => 'M', 'period_months' => 121], 'period_months'],
    'installment period text' => ['installments', ['code' => 'I1', 'name' => 'M', 'period_months' => 'abc'], 'period_months'],
    'installment period fraction' => ['installments', ['code' => 'I1', 'name' => 'M', 'period_months' => 1.5], 'period_months'],
    'installment period missing' => ['installments', ['code' => 'I1', 'name' => 'M'], 'period_months'],
    'method no code' => ['interest-methods', ['code' => '', 'name' => 'M'], 'code'],
    'method no name' => ['interest-methods', ['code' => 'M1', 'name' => ' '], 'name'],
    'type name over 100' => ['collateral-types', ['code' => '05', 'name' => str_repeat('x', 101)], 'name'],
    'binding code over 8' => ['collateral-bindings', ['code' => '123456789', 'name' => 'B'], 'code'],
    'condition name as array' => ['collateral-conditions', ['code' => '9', 'name' => ['x']], 'name'],
    'valuation no code' => ['collateral-valuations', ['code' => '', 'name' => 'V'], 'code'],
    'classification unknown type' => ['collateral-classifications', ['collateral_type_code' => '99', 'code' => '01', 'name' => 'C'], 'collateral_type_code'],
    'classification no type' => ['collateral-classifications', ['code' => '01', 'name' => 'C'], 'collateral_type_code'],
]);

it('answers one validation sentence for hostile values and saves nothing', function (string $slug, array $input, string $field) {
    CollateralType::create(['code' => '05', 'name' => 'LAND']);
    $model = config("references.$slug.model");

    $this->post(route('references.store', $slug), $input)->assertSessionHasErrors($field);

    expect($model::query()->count())->toBe($model === CollateralType::class ? 1 : 0);
})->with('hostile');

it('takes the same code once per collateral type for a classification, and once overall elsewhere', function () {
    CollateralType::create(['code' => '05', 'name' => 'LAND']);
    CollateralType::create(['code' => '06', 'name' => 'VEHICLE']);
    Product::create(['code' => 'P1', 'alias' => 'PA', 'name' => 'P', 'is_active' => true]);

    $classification = fn (string $type, string $code) => $this->post(route('references.store', 'collateral-classifications'), ['collateral_type_code' => $type, 'code' => $code, 'name' => 'C']);

    $classification('05', '01')->assertSessionHasNoErrors();
    $classification('05', '01')->assertSessionHasErrors('code');
    $classification('06', '01')->assertSessionHasNoErrors();
    $this->post(route('references.store', 'products'), ['code' => 'p1', 'alias' => 'PB', 'name' => 'Q', 'is_active' => true])->assertSessionHasErrors('code');
    $this->post(route('references.store', 'products'), ['code' => 'P2', 'alias' => 'pa', 'name' => 'Q', 'is_active' => true])->assertSessionHasErrors('alias');
});

it('writes the audit entry of every change for every table', function (string $slug, Closure $make) {
    AuditLog::query()->getQuery()->delete();
    $row = $make();
    $table = $row->getTable();
    $input = Arr::only($row->getAttributes(), array_column(config("references.$slug.fields"), 'name'));

    $this->put(route('references.update', [$slug, $row->id]), [...$input, 'name' => 'RENAMED']);
    $this->delete(route('references.destroy', [$slug, $row->id]));

    expect(AuditLog::query()->where('event', 'like', "$table.%")->orderBy('id')->pluck('event')->all())->toBe(["$table.created", "$table.updated", "$table.deleted"]);
})->with('free rows');

it('keeps the code of a row that others refer to by code, and lets a row that is referred to by id change its code', function (string $slug, Closure $make, Closure $hold, bool $locked) {
    $row = $make();
    $hold($row);
    $input = [...Arr::only($row->getAttributes(), ['code', 'alias', 'name', 'period_months', 'is_active']), 'code' => 'ZZ'];

    $this->put(route('references.update', [$slug, $row->id]), $input);

    expect($row->fresh()->code)->toBe($locked ? $row->code : 'ZZ');
})->with([
    'type by collateral' => ['collateral-types', fn () => CollateralType::create(['code' => '05', 'name' => 'LAND']), fn (CollateralType $t) => Collateral::create(['collateral_type_code' => $t->code]), true],
    'binding by collateral' => ['collateral-bindings', fn () => BindingType::create(['code' => '01', 'name' => 'APHT']), fn (BindingType $b) => Collateral::create(['collateral_type_code' => '05', 'binding_type_code' => $b->code]), true],
    'condition by collateral' => ['collateral-conditions', fn () => CollateralCondition::create(['code' => '9', 'name' => 'OK']), fn (CollateralCondition $c) => Collateral::create(['collateral_type_code' => '05', 'condition_code' => $c->code]), true],
    'valuation by collateral' => ['collateral-valuations', fn () => CollateralMethod::create(['code' => '1', 'name' => 'LANCAR']), fn (CollateralMethod $m) => Collateral::create(['collateral_type_code' => '05', 'ppap_code' => $m->code]), true],
    'method by application' => ['interest-methods', fn () => Method::create(['code' => 'M1', 'name' => 'FLAT']), fn (Method $m) => loanWith(['method_id' => $m->id]), false],
    'installment by application' => ['installments', fn () => Installment::create(['code' => 'I1', 'name' => 'MONTHLY', 'period_months' => 1]), fn (Installment $i) => loanWith(['installment_id' => $i->id]), false],
    'product by application' => ['products', fn () => Product::create(['code' => 'P1', 'alias' => 'PA', 'name' => 'P', 'is_active' => true]), fn (Product $p) => loanWith(['product_id' => $p->id]), false],
]);

it('records the loss of the parameters of a product that is deleted with it', function () {
    $product = Product::create(['code' => 'P1', 'alias' => 'PA', 'name' => 'P', 'is_active' => true]);
    ProductParameter::create(['product_id' => $product->id, 'max_amount' => 50_000_000, 'max_tenor' => 24]);
    AuditLog::query()->getQuery()->delete();

    $this->delete(route('references.destroy', ['products', $product->id]))->assertSessionHas('success');

    $deleted = AuditLog::query()->where('event', 'product_parameters.deleted')->first();

    expect($deleted)->not->toBeNull()->and($deleted->old_values)->toContain('50000000');
});
