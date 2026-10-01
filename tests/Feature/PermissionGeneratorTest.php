<?php

use App\Audit\AuditLog;
use App\Models\Permission;
use Database\Seeders\PermissionSeeder;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;

it('lists every permission with its type and the number of roles holding it', function () {
    $admin = superAdmin();

    $this->actingAs($admin)->get(route('permissions.index'))
        ->assertInertia(fn (Assert $page) => $page->component('permissions/index')
            ->has('permissions.data', 10)
            ->where('permissions.data.0.custom', false)
            ->where('permissions.data.0.name', 'analysis.view')
            ->has('entities', 6));

    $this->get(route('permissions.index', ['search' => 'surveys']))
        ->assertInertia(fn (Assert $page) => $page->has('permissions.data', 2));
    $this->get(route('permissions.index', ['entity' => 'dashboard', 'type' => 'system']))
        ->assertInertia(fn (Assert $page) => $page->has('permissions.data', 1));
    $this->get(route('permissions.index', ['type' => 'custom']))
        ->assertInertia(fn (Assert $page) => $page->has('permissions.data', 0));
});

it('generates standard permissions, skips existing ones and gives them to Super Admin', function () {
    $admin = superAdmin();
    $this->actingAs($admin);

    $this->post(route('permissions.generate'), ['entity' => 'Credit Analysis', 'actions' => ['view', 'create', 'update']])->assertSessionHasNoErrors();

    expect(Permission::where('is_custom', true)->orderBy('name')->pluck('name')->all())->toBe(['credit-analysis.create', 'credit-analysis.update', 'credit-analysis.view'])
        ->and(Role::findByName('Super Admin')->hasPermissionTo('credit-analysis.create'))->toBeTrue()
        ->and($admin->fresh()->can('credit-analysis.update'))->toBeTrue();

    $this->post(route('permissions.generate'), ['entity' => 'credit-analysis', 'actions' => ['view', 'delete']])
        ->assertSessionHas('success', '1 izin berhasil dibuat, 1 sudah ada sebelumnya.');
    expect(Permission::where('name', 'credit-analysis.delete')->exists())->toBeTrue();

    $row = AuditLog::query()->where('event', 'permissions.generated')->latest('id')->firstOrFail();
    expect($row->decoded('context')['skipped'])->toBe(['credit-analysis.view']);
});

it('does not duplicate a system permission and validates the input', function () {
    $this->actingAs(superAdmin());

    $this->post(route('permissions.generate'), ['entity' => 'surveys', 'actions' => ['view', 'manage']])->assertSessionHasErrors('actions.1');
    $this->post(route('permissions.generate'), ['entity' => 'surveys', 'actions' => ['view']])->assertSessionHas('success', '0 izin berhasil dibuat, 1 sudah ada sebelumnya.');
    $this->post(route('permissions.generate'), ['entity' => '', 'actions' => ['view']])->assertSessionHasErrors('entity');
    $this->post(route('permissions.generate'), ['entity' => 'Bad!Name', 'actions' => ['view']])->assertSessionHasErrors('entity');
    $this->post(route('permissions.generate'), ['entity' => 'ok', 'actions' => []])->assertSessionHasErrors('actions');
    expect(Permission::where('is_custom', true)->count())->toBe(0);
});

it('offers custom permissions in the role matrix and lets a role hold them', function () {
    $this->actingAs(superAdmin());
    $this->post(route('permissions.generate'), ['entity' => 'reports', 'actions' => ['view_any']]);
    $role = Role::findOrCreate('Auditor', 'web');

    $this->get(route('roles.show', $role))->assertInertia(fn (Assert $page) => $page
        ->where('matrix.2.group', 'Kustom')
        ->where('matrix.2.modules.0.abilities.0.name', 'reports.view_any')
        ->where('matrix.2.modules.0.abilities.0.label', 'Lihat semua'));

    $this->put(route('roles.permissions', $role), ['permissions' => ['reports.view_any']])->assertSessionHasNoErrors();
    expect($role->fresh()->hasPermissionTo('reports.view_any'))->toBeTrue();
});

it('keeps custom permissions when the seeder runs again and removes stale system ones', function () {
    $this->actingAs(superAdmin());
    $this->post(route('permissions.generate'), ['entity' => 'reports', 'actions' => ['view']]);
    Permission::create(['name' => 'retired.view', 'guard_name' => 'web']);

    $this->seed(PermissionSeeder::class);

    expect(Permission::where('name', 'reports.view')->exists())->toBeTrue()
        ->and(Permission::where('name', 'retired.view')->exists())->toBeFalse();
});

it('only deletes custom permissions that no role holds', function () {
    $this->actingAs(superAdmin());
    $this->post(route('permissions.generate'), ['entity' => 'reports', 'actions' => ['view', 'create']]);
    $held = Permission::where('name', 'reports.view')->firstOrFail();
    $free = Permission::where('name', 'reports.create')->firstOrFail();
    Role::findByName('Super Admin')->revokePermissionTo($free);
    Role::findOrCreate('Auditor', 'web')->givePermissionTo($held);

    $this->delete(route('permissions.destroy', Permission::where('name', 'dashboard.view')->firstOrFail()))->assertForbidden();
    $this->delete(route('permissions.destroy', $held))->assertSessionHas('error');
    $this->delete(route('permissions.destroy', $free))->assertSessionHas('success');

    expect(Permission::where('name', 'reports.view')->exists())->toBeTrue()
        ->and(Permission::where('name', 'reports.create')->exists())->toBeFalse()
        ->and(AuditLog::query()->where('event', 'permissions.deleted')->exists())->toBeTrue();
});

it('keeps the permission generator for Super Admin only', function () {
    $this->actingAs(userWith(['dashboard.view']));

    $this->post(route('permissions.generate'), ['entity' => 'reports', 'actions' => ['view']])->assertForbidden();
    expect(Permission::where('is_custom', true)->count())->toBe(0);
});
