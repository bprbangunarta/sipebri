<?php

use App\Audit\AuditLog;
use App\Enums\RoleName;
use App\Models\CommitteePath;
use App\Models\CommitteeTier;
use App\Models\Permission;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->actingAs(superAdmin());
});

dataset('system roles', collect(RoleName::cases())->reject(fn (RoleName $r): bool => $r === RoleName::SuperAdmin)->mapWithKeys(fn (RoleName $r): array => [$r->value => [$r->value]])->all());

it('keeps a role that the code refers to by name: no rename, no delete, even when nobody holds it', function (string $name) {
    $role = Role::findByName($name, 'web');
    $role->users()->detach();

    $this->put(route('roles.update', $role), ['name' => $name.' Baru'])->assertSessionHasErrors('name');
    $this->delete(route('roles.destroy', $role))->assertSessionHas('error');

    expect(Role::query()->where('name', $name)->exists())->toBeTrue()->and(Role::query()->where('name', $name.' Baru')->exists())->toBeFalse();
})->with('system roles');

it('keeps the name of any other role while somebody holds it, active or deactivated, and lets a free one change', function (string $holder) {
    $role = Role::findOrCreate('Peran Lokal', 'web');
    $user = User::factory()->create()->assignRole($role);
    $holder === 'deactivated' && $user->delete();

    if ($holder === 'free') {
        $user->forceDelete();
        $this->put(route('roles.update', $role), ['name' => 'Peran Baru'])->assertSessionHasNoErrors();
        expect($role->fresh()->name)->toBe('Peran Baru');

        return;
    }

    $this->put(route('roles.update', $role), ['name' => 'Peran Baru'])->assertSessionHasErrors('name');
    expect($role->fresh()->name)->toBe('Peran Lokal');
})->with(['active person' => 'active', 'deactivated person' => 'deactivated', 'nobody' => 'free']);

it('refuses to delete a role that only a deactivated person holds, so that person does not lose it silently', function () {
    $role = Role::findOrCreate('Peran Lokal', 'web');
    $user = User::factory()->create()->assignRole($role);
    $user->delete();

    $this->delete(route('roles.destroy', $role))->assertSessionHas('error');

    expect(Role::query()->where('name', 'Peran Lokal')->exists())->toBeTrue()->and(User::withTrashed()->find($user->id)->hasRole('Peran Lokal'))->toBeTrue();
});

it('answers one validation sentence for a bad role name and saves nothing', function (mixed $name) {
    $count = Role::count();

    $this->post(route('roles.store'), ['name' => $name])->assertSessionHasErrors('name');

    expect(Role::count())->toBe($count);
})->with(['empty' => '', 'spaces only' => '   ', 'over 100' => str_repeat('x', 101), 'array' => [['x']], 'missing' => null]);

it('does not let a name with spaces at the edge slip past the unique check and collide in the database', function () {
    Role::findOrCreate('Auditor', 'web');

    $this->post(route('roles.store'), ['name' => ' Auditor '])->assertSessionHasErrors('name');
    $other = Role::findOrCreate('Lain', 'web');
    $this->put(route('roles.update', $other), ['name' => ' Auditor '])->assertSessionHasErrors('name');

    expect(Role::query()->where('name', 'like', '%Auditor%')->count())->toBe(1);
});

it('stores a role name that holds markup as plain text', function () {
    $this->post(route('roles.store'), ['name' => '<b>Peran</b> & "lain"'])->assertSessionHasNoErrors();

    expect(Role::query()->where('name', '<b>Peran</b> & "lain"')->exists())->toBeTrue();
});

it('says in Indonesian why a role cannot be renamed or deleted when a committee tier uses it', function () {
    $role = Role::findOrCreate('Peran Komite', 'web');
    $path = CommitteePath::create(['condition' => 'NORMAL', 'mechanism' => 'plafon', 'is_active' => true]);
    CommitteeTier::create(['committee_path_id' => $path->id, 'sort' => 1, 'label' => 'Uji', 'role' => 'Peran Komite', 'min_amount' => 0, 'max_amount' => 1, 'can_approve' => true]);

    $this->put(route('roles.update', $role), ['name' => 'Peran Baru'])->assertSessionHasErrors('name');
    $message = session('errors')->first('name');

    expect($message)->not->toContain('decides')->not->toContain('committee ')->toContain('jenjang komite');
});

it('writes no audit entry for a rename to the same name or a save of the same permissions', function () {
    $role = Role::findOrCreate('Peran Lokal', 'web');
    $role->syncPermissions(['dashboard.view']);
    AuditLog::query()->getQuery()->delete();

    $this->put(route('roles.update', $role), ['name' => 'Peran Lokal'])->assertSessionHasNoErrors();
    $this->put(route('roles.permissions', $role), ['permissions' => ['dashboard.view']])->assertSessionHasNoErrors();

    expect(AuditLog::query()->count())->toBe(0);
});

it('writes one audit entry for each real change of a role', function () {
    AuditLog::query()->getQuery()->delete();

    $this->post(route('roles.store'), ['name' => 'Peran Baru']);
    $role = Role::findByName('Peran Baru', 'web');
    $this->put(route('roles.update', $role), ['name' => 'Peran Lama']);
    $this->put(route('roles.permissions', $role), ['permissions' => ['loan-applications.manage']]);
    $this->delete(route('roles.destroy', $role));

    $events = AuditLog::query()->orderBy('id')->pluck('event')->all();
    $changed = AuditLog::query()->where('event', 'roles.permissions_changed')->firstOrFail();

    expect($events)->toContain('roles.created', 'roles.renamed', 'roles.permissions_changed', 'roles.deleted')
        ->and($changed->decoded('new_values')['added'])->toEqualCanonicalizing(['loan-applications.manage', 'loan-applications.view']);
});

it('refuses a permission that is in neither list, and pulls the view in with a manage', function () {
    $role = Role::findOrCreate('Peran Lokal', 'web');

    $this->put(route('roles.permissions', $role), ['permissions' => ['users.delete_everything']])->assertSessionHasErrors('permissions.0');
    $this->put(route('roles.permissions', $role), ['permissions' => ['collaterals.manage']])->assertSessionHasNoErrors();

    expect($role->fresh()->permissions->pluck('name')->sort()->values()->all())->toBe(['collaterals.manage', 'collaterals.view']);
});

it('keeps every action of both screens for Super Admin only', function (string $method, string $uri) {
    $role = Role::findOrCreate('Peran Lokal', 'web');
    $permission = Permission::create(['name' => 'uji.view', 'guard_name' => 'web', 'is_custom' => true]);
    $this->actingAs(userWith(['dashboard.view', 'loan-applications.view']));

    $this->{$method}(str_replace(['{role}', '{permission}'], [$role->id, $permission->id], $uri), ['name' => 'X', 'permissions' => [], 'entity' => 'x', 'actions' => ['view']])->assertForbidden();

    expect(Role::findByName('Peran Lokal', 'web')->name)->toBe('Peran Lokal')->and(Permission::query()->whereKey($permission->id)->exists())->toBeTrue();
})->with([
    ['get', '/roles'], ['get', '/roles/{role}'], ['post', '/roles'], ['put', '/roles/{role}'], ['put', '/roles/{role}/permissions'], ['delete', '/roles/{role}'],
    ['get', '/permissions'], ['post', '/permissions/generate'], ['delete', '/permissions/{permission}'],
]);

it('answers not found for a role or a permission that does not exist', function () {
    $this->get('/roles/99999')->assertNotFound();
    $this->put('/roles/99999', ['name' => 'X'])->assertNotFound();
    $this->delete('/roles/99999')->assertNotFound();
    $this->delete('/permissions/99999')->assertNotFound();
    $this->delete('/permissions/abc')->assertNotFound();
});

it('keeps the roles the seeder made when it runs again', function () {
    $this->seed(RoleSeeder::class);
    $before = Role::count();

    $this->seed(RoleSeeder::class);

    expect(Role::count())->toBe($before);
});

it('answers one validation sentence for a bad generator request and creates nothing', function (array $input, string $field) {
    $this->post(route('permissions.generate'), $input)->assertSessionHasErrors($field);

    expect(Permission::query()->where('is_custom', true)->count())->toBe(0);
})->with([
    'no entity' => [['actions' => ['view']], 'entity'],
    'entity over 50' => [['entity' => str_repeat('a', 51), 'actions' => ['view']], 'entity'],
    'entity with a leading hyphen' => [['entity' => '-abc', 'actions' => ['view']], 'entity'],
    'entity with a double hyphen' => [['entity' => 'a--b', 'actions' => ['view']], 'entity'],
    'entity with a dot' => [['entity' => 'a.b', 'actions' => ['view']], 'entity'],
    'entity with a slash' => [['entity' => 'a/b', 'actions' => ['view']], 'entity'],
    'entity with accents' => [['entity' => 'laporán', 'actions' => ['view']], 'entity'],
    'entity as array' => [['entity' => ['x'], 'actions' => ['view']], 'entity'],
    'action unknown' => [['entity' => 'ok', 'actions' => ['destroy_all']], 'actions.0'],
    'action as array' => [['entity' => 'ok', 'actions' => [['view']]], 'actions.0'],
    'actions as text' => [['entity' => 'ok', 'actions' => 'view'], 'actions'],
]);

it('makes the same permission once when an action is repeated, and normalises the entity name', function () {
    $this->post(route('permissions.generate'), ['entity' => '  Laporan   Harian_Bulanan ', 'actions' => ['view', 'view', 'create']])->assertSessionHasNoErrors();

    expect(Permission::query()->where('is_custom', true)->orderBy('name')->pluck('name')->all())->toBe(['laporan-harian-bulanan.create', 'laporan-harian-bulanan.view']);
});

it('writes no audit entry when the generator creates nothing new', function () {
    $this->post(route('permissions.generate'), ['entity' => 'uji', 'actions' => ['view']]);
    AuditLog::query()->getQuery()->delete();

    $this->post(route('permissions.generate'), ['entity' => 'uji', 'actions' => ['view']])->assertSessionHasNoErrors();

    expect(AuditLog::query()->count())->toBe(0);
});

it('refuses to delete a custom permission that a role holds, and a system permission, and records the delete of a free one', function () {
    $this->post(route('permissions.generate'), ['entity' => 'uji', 'actions' => ['view', 'create']]);
    $held = Permission::findByName('uji.view', 'web');
    $free = Permission::findByName('uji.create', 'web');
    Role::findOrCreate('Peran Lokal', 'web')->givePermissionTo($held);
    $system = Permission::findByName('dashboard.view', 'web');
    AuditLog::query()->getQuery()->delete();

    $this->delete(route('permissions.destroy', $held))->assertSessionHas('error');
    $this->delete(route('permissions.destroy', $system))->assertForbidden();
    $this->delete(route('permissions.destroy', $free))->assertSessionHas('success');

    expect(Permission::query()->where('name', 'uji.view')->exists())->toBeTrue()->and(Permission::query()->where('name', 'dashboard.view')->exists())->toBeTrue()
        ->and(Permission::query()->where('name', 'uji.create')->exists())->toBeFalse()
        ->and(AuditLog::query()->where('event', 'like', 'permissions.%')->pluck('event')->all())->toBe(['permissions.deleted'])
        ->and(AuditLog::query()->where('event', 'access.denied')->count())->toBe(1);
});

it('answers an empty list, not a server error, for any search or entity text', function (string $text) {
    $this->get(route('permissions.index', ['search' => $text, 'entity' => $text]))->assertOk();
})->with(['wildcard' => '%', 'underscore' => '_', 'slash' => '\\', 'long' => 'x', 'quote' => "'; drop table users; --"]);

dataset('permission sorts', function () {
    foreach (['name', 'module', 'type', 'roles'] as $key) {
        foreach (['asc', 'desc'] as $direction) {
            yield "$key $direction" => [$key, $direction];
        }
    }
});

it('sorts the permissions by every column in both directions', function (string $key, string $direction) {
    $this->post(route('permissions.generate'), ['entity' => 'aaa', 'actions' => ['view']]);
    $this->post(route('permissions.generate'), ['entity' => 'zzz', 'actions' => ['view']]);
    Role::findOrCreate('Peran Lokal', 'web')->givePermissionTo(['zzz.view', 'dashboard.view']);
    Role::findOrCreate('Peran Dua', 'web')->givePermissionTo(['zzz.view']);

    $rows = collect($this->get(route('permissions.index', ['sort' => $key, 'direction' => $direction, 'per_page' => 50]))->assertOk()->inertiaProps('permissions.data'));
    $names = $rows->pluck('name')->all();

    expect($names)->toHaveCount(13);

    $value = fn (array $row) => match ($key) {
        'name' => $row['name'], 'module' => (string) $row['module'], 'type' => (int) $row['custom'], 'roles' => $row['roles'],
    };
    $values = $rows->map($value)->all();
    $sorted = $values;
    $direction === 'asc' ? sort($sorted) : rsort($sorted);

    expect($values)->toBe($sorted);
})->with('permission sorts');

it('refuses a permission sort key that is not a column', function (string $key) {
    $this->get(route('permissions.index', ['sort' => $key]))->assertSessionHasErrors('sort');
})->with(['id', 'guard_name', 'password', 'is_custom']);

it('tells the role page whether the name is fixed, so the screen does not offer what the server refuses', function () {
    $held = Role::findOrCreate('Peran Lokal', 'web');
    User::factory()->create()->assignRole($held)->delete();
    $free = Role::findOrCreate('Peran Bebas', 'web');

    $props = fn (Role $role) => $this->get(route('roles.show', $role))->inertiaProps('role');

    expect($props(Role::findByName('Teller', 'web')))->toMatchArray(['named_by_code' => true])
        ->and($props($held))->toMatchArray(['named_by_code' => false, 'holders' => 1])
        ->and($props($free))->toMatchArray(['named_by_code' => false, 'holders' => 0]);
});
