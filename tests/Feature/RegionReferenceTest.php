<?php

use App\Audit\AuditLog;
use App\Models\Collateral;
use App\Models\Region;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->actingAs(superAdmin());
    $this->village = Region::create(['code' => '3201', 'regency' => 'Kab. Bogor', 'district' => 'Cibinong', 'village' => 'Pakansari', 'postal_code' => '16915']);
});

function regionInput(array $overrides = []): array
{
    return array_merge(['code' => '3201', 'regency' => 'Kab. Bogor', 'district' => 'Cibinong', 'village' => 'Harapan Jaya', 'postal_code' => '16914'], $overrides);
}

it('refuses to delete the last village of a code that a collateral uses, and allows it while another village keeps the code', function () {
    Collateral::create(['collateral_type_code' => '05', 'region_code' => '3201']);
    $other = Region::create(['code' => '3201', 'regency' => 'Kab. Bogor', 'district' => 'Cibinong', 'village' => 'Harapan Jaya']);

    $this->delete(route('references.destroy', ['regions', $other->id]))->assertSessionHas('success');

    $this->delete(route('references.destroy', ['regions', $this->village->id]))->assertSessionHas('error', fn (string $message): bool => str_contains($message, 'Pakansari') && str_contains($message, '1 agunan'));
    expect(Region::count())->toBe(1);

    Collateral::query()->delete();
    $this->delete(route('references.destroy', ['regions', $this->village->id]))->assertSessionHas('success');
});

it('refuses to change the code of the last village that a collateral uses', function () {
    Collateral::create(['collateral_type_code' => '05', 'region_code' => '3201']);

    $this->put(route('references.update', ['regions', $this->village->id]), regionInput(['code' => '3202', 'village' => 'Pakansari']))->assertSessionHasErrors('code');
    expect($this->village->fresh()->code)->toBe('3201');

    $this->put(route('references.update', ['regions', $this->village->id]), regionInput(['village' => 'Pakansari', 'postal_code' => '16999']))->assertSessionHasNoErrors();
    expect($this->village->fresh()->postal_code)->toBe('16999');

    Region::create(['code' => '3201', 'regency' => 'Kab. Bogor', 'district' => 'Cibinong', 'village' => 'Harapan Jaya']);
    $this->put(route('references.update', ['regions', $this->village->id]), regionInput(['code' => '3202', 'village' => 'Pakansari']))->assertSessionHasNoErrors();
    expect($this->village->fresh()->code)->toBe('3202');
});

it('lists one entry per code in the collateral picker, with every way the regency is written', function () {
    Region::create(['code' => '3408', 'regency' => 'Kab. Sijunjung', 'district' => 'A', 'village' => 'A']);
    Region::create(['code' => '3408', 'regency' => 'kota Sawahlunto', 'district' => 'B', 'village' => 'B']);
    Region::create(['code' => '3408', 'regency' => 'Kab. Sijunjung', 'district' => 'C', 'village' => 'C']);

    $this->get(route('collaterals.create'))->assertInertia(fn (Assert $page) => $page
        ->has('options.regions', 2)
        ->where('options.regions.1', ['value' => '3408', 'label' => '3408 : Kab. Sijunjung / kota Sawahlunto']));
});

it('answers one validation sentence for hostile values', function (array $overrides, string $field) {
    $this->post(route('references.store', 'regions'), regionInput($overrides))->assertSessionHasErrors($field);

    expect(Region::count())->toBe(1);
})->with([
    'code over 8' => [['code' => '123456789'], 'code'],
    'postal code over 8' => [['postal_code' => '123456789'], 'postal_code'],
    'regency over 100' => [['regency' => str_repeat('x', 101)], 'regency'],
    'district over 100' => [['district' => str_repeat('x', 101)], 'district'],
    'village over 100' => [['village' => str_repeat('x', 101)], 'village'],
    'code as array' => [['code' => ['3201']], 'code'],
    'village as array' => [['village' => ['x']], 'village'],
    'empty village' => [['village' => '  '], 'village'],
    'empty regency' => [['regency' => ''], 'regency'],
]);

it('stores markup as plain text and a postal code may stay empty', function () {
    $this->post(route('references.store', 'regions'), regionInput(['village' => '<b>x</b>', 'postal_code' => '']))->assertSessionHasNoErrors();

    $row = Region::query()->where('village', '<B>X</B>')->first(['village', 'postal_code']);

    expect($row)->not->toBeNull()->and($row->postal_code)->toBeNull();
});

it('answers not found for an id that does not exist', function () {
    $this->put(route('references.update', ['regions', 99999]), regionInput())->assertNotFound();
    $this->delete(route('references.destroy', ['regions', 99999]))->assertNotFound();
});

it('keeps every action for Super Admin only', function (string $method, string $uri) {
    $this->actingAs(userWith(['dashboard.view', 'collaterals.view', 'collaterals.manage']));

    $this->{$method}(str_replace('{id}', (string) $this->village->id, $uri), regionInput())->assertForbidden();

    expect(Region::count())->toBe(1);
})->with([['get', '/references/regions'], ['post', '/references/regions'], ['put', '/references/regions/{id}'], ['delete', '/references/regions/{id}']]);

it('writes the audit entry of every change and none for a save that changes nothing', function () {
    AuditLog::query()->getQuery()->delete();

    $this->post(route('references.store', 'regions'), regionInput());
    $new = Region::query()->where('village', 'HARAPAN JAYA')->firstOrFail();
    $this->put(route('references.update', ['regions', $new->id]), regionInput(['postal_code' => '16000']));
    $this->put(route('references.update', ['regions', $new->id]), regionInput(['postal_code' => '16000']));
    $this->delete(route('references.destroy', ['regions', $new->id]));

    expect(AuditLog::query()->orderBy('id')->pluck('event')->all())->toBe(['regions.created', 'regions.updated', 'regions.deleted']);
});

it('names a village by its village field in the page props, and other tables by their name', function () {
    $this->get(route('references.index', 'regions'))->assertInertia(fn (Assert $page) => $page->where('resource.title_field', 'village'));
    $this->get(route('references.index', 'offices'))->assertInertia(fn (Assert $page) => $page->where('resource.title_field', 'name'));
    $this->get(route('references.index', 'regions'))->assertInertia(fn (Assert $page) => $page->where('resource.tracks_usage', false));
});
