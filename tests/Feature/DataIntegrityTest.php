<?php

use App\Models\BindingType;
use App\Models\Collateral;
use App\Models\CollateralType;
use App\Models\CommitteePath;
use App\Models\CommitteeTier;
use Spatie\Permission\Models\Role;

it('refuses to delete reference data that collaterals refer to by code', function () {
    $type = CollateralType::create(['code' => '05', 'name' => 'LAND']);
    $binding = BindingType::create(['code' => '01', 'name' => 'FIDUCIARY']);
    Collateral::create(['collateral_type_code' => '05', 'binding_type_code' => '01']);
    $this->actingAs(superAdmin());

    $this->delete(route('master-data.destroy', ['collateral-types', $type->id]))->assertSessionHas('error');
    $this->delete(route('master-data.destroy', ['collateral-bindings', $binding->id]))->assertSessionHas('error');
    expect(CollateralType::count())->toBe(1)->and(BindingType::count())->toBe(1);

    Collateral::query()->delete();
    $this->delete(route('master-data.destroy', ['collateral-types', $type->id]))->assertSessionHas('success');
});

it('refuses to change a code that other records refer to', function () {
    $type = CollateralType::create(['code' => '05', 'name' => 'LAND']);
    Collateral::create(['collateral_type_code' => '05']);
    $this->actingAs(superAdmin());

    $this->put(route('master-data.update', ['collateral-types', $type->id]), ['code' => '06', 'name' => 'LAND'])->assertSessionHasErrors('code');
    expect($type->fresh()->code)->toBe('05');

    $this->put(route('master-data.update', ['collateral-types', $type->id]), ['code' => '05', 'name' => 'LAND AND BUILDINGS'])->assertSessionHasNoErrors();
    expect($type->fresh()->name)->toBe('LAND AND BUILDINGS');
});

it('does not delete or rename a role that decides in a committee tier', function () {
    $admin = superAdmin();
    $role = Role::findOrCreate('Kepala Test', 'web');
    $path = CommitteePath::create(['condition' => 'NORMAL', 'mechanism' => 'plafon', 'is_active' => true]);
    CommitteeTier::create(['committee_path_id' => $path->id, 'sort' => 1, 'label' => 'Test', 'role' => 'Kepala Test', 'min_amount' => 0, 'max_amount' => 100]);
    $this->actingAs($admin);

    $this->delete(route('roles.destroy', $role))->assertSessionHas('error');
    $this->put(route('roles.update', $role), ['name' => 'Renamed'])->assertSessionHasErrors('name');
    expect($role->fresh()->name)->toBe('Kepala Test');

    CommitteeTier::query()->delete();
    $this->delete(route('roles.destroy', $role))->assertSessionHas('success');
});
