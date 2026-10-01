<?php

use App\Enums\RoleName;
use App\Models\AppNotification;
use App\Models\Collateral;
use App\Models\User;
use App\Support\Notify;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;

it('denies routes the role has no permission for', function (string $method, string $uri) {
    $this->actingAs(userWith(['dashboard.view']))->{$method}($uri)->assertForbidden();
})->with([
    ['get', '/loan-applications'],
    ['get', '/collaterals/create'],
    ['get', '/references/regions'],
    ['post', '/references/regions'],
    ['get', '/committees'],
    ['get', '/users'],
    ['get', '/roles'],
]);

it('lets view-only users read but not change collaterals', function () {
    $this->actingAs(userWith(['collaterals.view']));
    $collateral = Collateral::create(['collateral_type_code' => '05']);

    $this->get(route('collaterals.index'))->assertOk();
    $this->get(route('collaterals.create'))->assertForbidden();
    $this->delete(route('collaterals.destroy', $collateral))->assertForbidden();
    expect(Collateral::count())->toBe(1);
});

it('only shares navigation the user may open', function () {
    $this->actingAs(userWith(['dashboard.view', 'loan-applications.view']))
        ->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->has('navigation', 1)
            ->where('navigation.0.items.0.label', 'Dashboard')
            ->where('navigation.0.items.1.label', 'Pengajuan'));

    $this->actingAs(superAdmin())->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page->where('navigation.2.label', 'Pengaturan')->where('navigation.1.label', 'Referensi'));
});

it('has no way to create, change or delete users here: accounts come from Codex', function () {
    $this->actingAs(superAdmin());

    foreach ([['post', '/users'], ['put', '/users/1'], ['delete', '/users/1']] as [$method, $uri]) {
        $this->{$method}($uri)->assertClientError();
    }
});

it('protects the Super Admin role and roles in use', function () {
    $this->actingAs(superAdmin());
    $super = Role::findByName(RoleName::SuperAdmin->value);
    $viewer = Role::findOrCreate('Viewer', 'web');

    $this->put(route('roles.permissions', $super), ['permissions' => []])->assertForbidden();
    $this->delete(route('roles.destroy', $super))->assertForbidden();

    $this->put(route('roles.permissions', $viewer), ['permissions' => ['collaterals.manage']])->assertSessionHasNoErrors();
    expect($viewer->fresh()->permissions->pluck('name')->sort()->values()->all())->toBe(['collaterals.manage', 'collaterals.view']);

    User::factory()->create()->assignRole($viewer);
    $this->delete(route('roles.destroy', $viewer))->assertSessionHas('error');
});

it('notifies permission holders except the actor and lets users mark their own as read', function () {
    $actor = userWith(['collaterals.manage'], 'Editor');
    $peer = userWith(['collaterals.manage'], 'Editor');
    $other = userWith(['dashboard.view'], 'Other');

    $this->actingAs($actor);
    expect(Notify::toPermission('collaterals.manage', 'Hello', 'collaterals'))->toBe(1);

    $notification = AppNotification::firstWhere('user_id', $peer->id);
    expect(AppNotification::where('user_id', $other->id)->exists())->toBeFalse();

    $this->actingAs($other)->post(route('notifications.read', $notification))->assertForbidden();
    $this->actingAs($peer)->post(route('notifications.read', $notification))->assertRedirect();
    expect($notification->fresh()->read_at)->not->toBeNull();
});

it('shows the empty permissions index only to Super Admin', function () {
    $this->actingAs(superAdmin())->get(route('permissions.index'))
        ->assertInertia(fn (Assert $page) => $page->component('permissions/index'));

    $this->actingAs(userWith(['dashboard.view'], 'Nobody'))->get(route('permissions.index'))->assertForbidden();
});

it('lists inactive users too and filters them by status', function () {
    $admin = superAdmin();
    $active = User::factory()->create(['name' => 'Active Person']);
    $inactive = User::factory()->create(['name' => 'Inactive Person']);
    $inactive->delete();
    $this->actingAs($admin);

    $names = fn (array $query) => $this->get(route('users.index', $query))->viewData('page')['props']['users']['data'];

    expect(collect($names([]))->pluck('name')->all())->toContain('Active Person', 'Inactive Person');
    expect(collect($names(['status' => 'inactive']))->pluck('name')->all())->toBe(['Inactive Person']);
    expect(collect($names(['status' => 'active']))->pluck('name')->all())->not->toContain('Inactive Person')->toContain('Active Person');
    expect(collect($names([]))->firstWhere('name', 'Inactive Person'))->toMatchArray(['active' => false]);
    $this->get(route('users.index', ['status' => 'bogus']))->assertSessionHasErrors('status');
});

it('lists roles as a searchable, sortable, paginated table', function () {
    $this->actingAs(superAdmin());

    $this->get(route('roles.index', ['per_page' => 10]))->assertInertia(fn (Assert $page) => $page
        ->component('roles/index')
        ->has('roles.data', 10)
        ->where('roles.total', 46)
        ->where('filters.sort', 'name'));

    $this->get(route('roles.index', ['search' => 'Teller']))->assertInertia(fn (Assert $page) => $page
        ->has('roles.data', 1)
        ->where('roles.data.0.name', 'Teller'));

    $this->get(route('roles.index', ['sort' => 'permissions_count', 'direction' => 'desc']))->assertInertia(fn (Assert $page) => $page
        ->where('roles.data.0.name', 'Super Admin')
        ->where('roles.data.0.locked', true));

    $this->get(route('roles.index', ['sort' => 'password']))->assertSessionHasErrors('sort');
});
