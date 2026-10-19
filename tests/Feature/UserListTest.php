<?php

use App\Models\Office;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->actingAs(superAdmin());
    $this->office = Office::create(['code' => '00', 'alias' => 'PMK', 'name' => 'Pamanukan']);
});

function person(string $name, array $attributes = [], string $role = 'Customer Service'): User
{
    return User::factory()->create(['name' => $name, 'username' => strtolower(str_replace(' ', '', $name)), 'email' => strtolower(str_replace(' ', '', $name)).'@contoh.test', ...$attributes])->assignRole($role);
}

it('lists a person who is deactivated, and narrows the list by status', function () {
    person('Aktif Satu');
    person('Mati Dua')->delete();

    $names = fn (?string $status): array => collect($this->get(route('users.index', array_filter(['status' => $status, 'per_page' => 50])))->inertiaProps('users.data'))->pluck('name')->all();

    expect($names(null))->toContain('Aktif Satu')->toContain('Mati Dua')
        ->and($names('active'))->toContain('Aktif Satu')->not->toContain('Mati Dua')
        ->and($names('inactive'))->toBe(['Mati Dua']);
});

it('answers an empty list, not a server error, for a role that does not exist', function (string $role) {
    $this->get(route('users.index', ['role' => $role]))->assertOk()->assertInertia(fn (Assert $page) => $page->where('users.total', 0));
})->with(['unknown' => 'Tidak Ada', 'wildcard' => '%', 'long' => str_repeat('x', 150)]);

it('refuses a status, a per-page count, and a sort key that the list does not know', function (array $query, string $field) {
    $this->get(route('users.index', $query))->assertSessionHasErrors($field);
})->with([
    'status' => [['status' => 'deleted'], 'status'],
    'per page as text' => [['per_page' => 'abc'], 'per_page'],
    'sort by a password' => [['sort' => 'password'], 'sort'],
    'sort by id' => [['sort' => 'id'], 'sort'],
    'sort by the national id' => [['sort' => 'nik_hash'], 'sort'],
    'direction sideways' => [['direction' => 'up'], 'direction'],
    'search too long' => [['search' => str_repeat('x', 101)], 'search'],
]);

it('falls back to 25 rows for a page size that is not offered', function (int $asked) {
    $this->get(route('users.index', ['per_page' => $asked]))->assertInertia(fn (Assert $page) => $page->where('filters.per_page', 25));
})->with([0, 7, 999, -5]);

it('searches % and _ as the characters they are', function () {
    person('Biasa Saja');
    person('Seratus Persen', ['email' => 'seratus%persen@contoh.test']);

    $emails = fn (string $term): array => collect($this->get(route('users.index', ['search' => $term]))->inertiaProps('users.data'))->pluck('email')->all();

    // SQLite has no default escape character (MySQL does), so only what is the same on both is asserted: a wildcard never matches every row.
    expect($emails('%'))->not->toContain('biasasaja@contoh.test')->and($emails('_'))->not->toContain('biasasaja@contoh.test');
});

dataset('user sorts', function () {
    foreach (['name', 'username', 'email', 'role', 'office', 'status', 'created'] as $key) {
        foreach (['asc', 'desc'] as $direction) {
            yield "$key $direction" => [$key, $direction];
        }
    }
});

it('sorts the list by every column in both directions', function (string $key, string $direction) {
    $other = Office::create(['code' => '01', 'alias' => 'CGK', 'name' => 'Jalancagak']);
    $rows = [
        ['Citra', 'alpha', 'zeta@contoh.test', 'Direktur Utama', $this->office, '2026-01-03', false],
        ['Adi', 'mike', 'alpha@contoh.test', 'Customer Service', $other, '2026-01-02', true],
        ['Bani', 'zulu', 'mike@contoh.test', 'Staff Legal', null, '2026-01-01', false],
    ];

    foreach ($rows as [$name, $username, $email, $role, $office, $created, $gone]) {
        $user = person($name, ['username' => $username, 'email' => $email, 'office_id' => $office?->id, 'created_at' => $created], $role);
        $gone && $user->delete();
    }

    $names = collect($this->get(route('users.index', ['sort' => $key, 'direction' => $direction, 'per_page' => 50]))->assertOk()->inertiaProps('users.data'))
        ->reject(fn (array $u): bool => in_array($u['name'], ['IT Support'], true) || $u['role'] === 'Super Admin')->pluck('name')->values()->all();
    $expected = [
        'name' => ['Adi', 'Bani', 'Citra'], 'username' => ['Citra', 'Adi', 'Bani'], 'email' => ['Adi', 'Bani', 'Citra'], 'role' => ['Adi', 'Citra', 'Bani'],
        'office' => ['Bani', 'Adi', 'Citra'], 'status' => ['Bani', 'Citra', 'Adi'], 'created' => ['Bani', 'Adi', 'Citra'],
    ][$key];
    // Ties are always broken by name, ascending, whatever the direction: the two people who are active tie on status.
    $expectedDesc = $key === 'status' ? ['Adi', 'Bani', 'Citra'] : array_reverse($expected);

    expect($names)->toBe($direction === 'asc' ? $expected : $expectedDesc);
})->with('user sorts');

it('never sends a national id, a password, or a token to the browser', function () {
    $user = person('Rahasia Besar');
    $user->setNik('3201010101010001', 'manual');

    $json = json_encode($this->get(route('users.index', ['per_page' => 50]))->inertiaProps('users'));

    expect($json)->not->toContain('3201010101010001')->not->toContain('nik')->not->toContain('password')->not->toContain('remember_token')->not->toContain($user->password);
});

it('keeps the list for Super Admin only', function () {
    $this->actingAs(userWith(['dashboard.view', 'loan-applications.view']));

    $this->get(route('users.index'))->assertForbidden();
});

it('seeds invented people only, adds the missing ones, and never touches a person who already exists', function () {
    $this->seed([UserSeeder::class]);
    $before = User::withTrashed()->count();

    $person = User::withTrashed()->findOrFail(30);
    $person->update(['name' => 'Diganti Admin', 'email' => 'diganti@contoh.test']);
    $person->syncRoles(['Super Admin']);
    $this->seed(UserSeeder::class);

    $source = (string) file_get_contents(database_path('seeders/UserSeeder.php'));

    expect($before)->toBe(28)->and(User::withTrashed()->count())->toBe(28)
        ->and($person->fresh()->name)->toBe('Diganti Admin')->and($person->fresh()->hasRole('Super Admin'))->toBeTrue()
        ->and(preg_match('/gmail|yahoo|hotmail|bprbangunarta|\.ac\.id/i', $source))->toBe(0)
        ->and(User::withTrashed()->where('id', '!=', 1)->pluck('email')->every(fn (string $email): bool => str_ends_with($email, '@contoh.test')))->toBeTrue();
});
