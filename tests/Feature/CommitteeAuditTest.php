<?php

use App\Audit\AuditLog;
use App\Models\CommitteePath;
use App\Models\LoanApplication;
use App\Models\Office;
use App\Models\Product;
use App\Models\User;
use App\Support\CommitteeLevels;
use Database\Seeders\CommitteeSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Arr;

beforeEach(function () {
    $this->actingAs(superAdmin());
});

/** A product, the default levels, and a path of its own with one level. */
function committeeFixture(): array
{
    $product = Product::create(['code' => 'P1', 'alias' => 'KRU', 'name' => 'KRU', 'is_active' => true]);
    test()->seed([RoleSeeder::class, CommitteeSeeder::class]);
    $default = CommitteeLevels::defaultPath();
    $own = CommitteePath::create(['product_id' => $product->id, 'condition' => 'EXTRA', 'mechanism' => 'plafon', 'is_active' => true, 'follows_default' => false]);
    $own->tiers()->create(['sort' => 1, 'label' => 'A', 'role' => 'Direktur Utama', 'min_amount' => 0, 'max_amount' => null, 'can_approve' => true]);

    return ['product' => $product, 'default' => $default, 'own' => $own, 'tier' => $own->tiers()->first()];
}

function tierInput(array $overrides = []): array
{
    return array_merge(['label' => 'Seksi', 'role' => 'Direktur Bisnis', 'min_amount' => 0, 'max_amount' => 1000, 'can_approve' => true], $overrides);
}

it('keeps the default path as it is: only its levels change, not its identity or whether it exists', function (array $change) {
    ['default' => $default] = committeeFixture();
    $before = Arr::only($default->getAttributes(), ['product_id', 'condition', 'mechanism', 'is_active']);

    $this->put(route('committees.update', $default), [...$before, ...$change])->assertForbidden();

    expect(Arr::only($default->fresh()->getAttributes(), ['product_id', 'condition', 'mechanism', 'is_active']))->toBe($before);
})->with([
    'other mechanism' => [['mechanism' => 'hierarki']],
    'a product' => [['product_id' => 1]],
    'a condition' => [['condition' => 'RELOAN']],
    'inactive' => [['is_active' => false]],
]);

it('refuses to delete the last level of the default path, which every follower copies', function () {
    ['default' => $default] = committeeFixture();
    foreach ($default->tiers()->orderByDesc('sort')->get()->slice(1) as $tier) {
        $this->delete(route('committees.tiers.destroy', [$default, $tier]))->assertSessionHas('success');
    }

    $last = $default->tiers()->firstOrFail();
    $followers = CommitteePath::query()->where('follows_default', true)->first();

    $this->delete(route('committees.tiers.destroy', [$default, $last]))->assertSessionHas('error');

    expect($default->tiers()->count())->toBe(1)->and($followers->fresh()->tiers()->count())->toBe(1);
});

it('refuses to delete a path that a deleted application still holds, in one sentence', function () {
    ['own' => $own] = committeeFixture();
    LoanApplication::create([
        'application_code' => '00700001', 'application_date' => now(), 'status' => 'draft', 'nik' => '1', 'full_name' => 'A',
        'created_by' => auth()->id(), 'committee_path_id' => $own->id,
    ])->delete();

    $this->delete(route('committees.destroy', $own))->assertSessionHas('error');

    expect(CommitteePath::find($own->id))->not->toBeNull();
});

it('answers not found for a level that belongs to another path', function (string $method, string $uri) {
    ['product' => $product, 'own' => $own] = committeeFixture();
    $other = CommitteePath::create(['product_id' => $product->id, 'condition' => 'OTHER', 'mechanism' => 'plafon', 'is_active' => true, 'follows_default' => false]);
    $stranger = $other->tiers()->create(['sort' => 1, 'label' => 'X', 'role' => 'Direktur Bisnis', 'min_amount' => 0, 'can_approve' => true]);

    $this->{$method}(str_replace(['{path}', '{tier}'], [$own->id, $stranger->id], $uri), tierInput())->assertNotFound();

    expect($stranger->fresh()->label)->toBe('X')->and($other->tiers()->count())->toBe(1);
})->with([['put', '/committees/{path}/tiers/{tier}'], ['delete', '/committees/{path}/tiers/{tier}'], ['put', '/committees/{path}/tiers/{tier}/move/up']]);

it('answers one validation sentence for a bad level and saves nothing', function (array $overrides, string $field) {
    ['own' => $own] = committeeFixture();
    $this->post(route('committees.tiers.store', $own), tierInput($overrides))->assertSessionHasErrors($field);

    expect($own->tiers()->count())->toBe(1);
})->with([
    'no role' => [['role' => ''], 'role'],
    'unknown role' => [['role' => 'Tidak Ada'], 'role'],
    'role as array' => [['role' => ['x']], 'role'],
    'label over 50' => [['label' => str_repeat('x', 51)], 'label'],
    'negative minimum' => [['min_amount' => -1], 'min_amount'],
    'maximum under minimum' => [['min_amount' => 500, 'max_amount' => 100], 'max_amount'],
    'amount as text' => [['min_amount' => 'abc'], 'min_amount'],
    'fractional amount' => [['max_amount' => 10.5], 'max_amount'],
    'decision as text' => [['can_approve' => 'maybe'], 'can_approve'],
]);

it('answers one validation sentence for a bad path and saves nothing', function (array $overrides, string $field) {
    ['product' => $product] = committeeFixture();
    $count = CommitteePath::count();

    $this->post(route('committees.store'), array_merge(['product_id' => $product->id, 'condition' => 'NEW', 'mechanism' => 'plafon', 'is_active' => true], $overrides))->assertSessionHasErrors($field);

    expect(CommitteePath::count())->toBe($count);
})->with([
    'duplicate product and condition' => [['condition' => 'extra'], 'condition'],
    'condition over 30' => [['condition' => str_repeat('x', 31)], 'condition'],
    'unknown mechanism' => [['mechanism' => 'acak'], 'mechanism'],
    'unknown product' => [['product_id' => 99999], 'product_id'],
    'unknown source path' => [['copy_from' => 99999], 'copy_from'],
    'note over 255' => [['note' => str_repeat('x', 256)], 'note'],
]);

it('lists members for any role text without a server error', function (string $role) {
    $this->get(route('committees.members', ['role' => $role]))->assertOk();
})->with(['unknown' => 'Tidak Ada', 'wildcard' => '%', 'long' => str_repeat('x', 100)]);

it('answers a hostile authority question with a validation error, never a server error', function (array $query) {
    $this->getJson(route('committees.authority', $query))->assertUnprocessable();
})->with([
    'no amount' => [[]],
    'negative amount' => [['amount' => -5]],
    'text amount' => [['amount' => 'abc']],
    'unknown product' => [['amount' => 1000, 'product_id' => 99999]],
    'applicant who is not a member' => [['amount' => 1000, 'applicant_id' => 1]],
    'condition over 30' => [['amount' => 1000, 'condition' => str_repeat('x', 31)]],
]);

it('keeps every page and action of Data Komite for Super Admin only', function (string $method, string $uri) {
    ['own' => $own, 'tier' => $tier] = committeeFixture();
    $this->actingAs(userWith(['dashboard.view', 'loan-applications.view', 'approvals.view']));

    $this->{$method}(str_replace(['{path}', '{tier}', '{user}'], [$own->id, $tier->id, auth()->id()], $uri))->assertForbidden();
})->with([
    ['get', '/committees'], ['get', '/committees/levels'], ['get', '/committees/special-rules'], ['get', '/committees/members'], ['get', '/committees/exceptions'],
    ['get', '/committees/authority?amount=1'], ['get', '/committees/{path}'], ['post', '/committees'], ['put', '/committees/{path}'], ['delete', '/committees/{path}'],
    ['put', '/committees/{path}/follow'], ['post', '/committees/{path}/tiers'], ['put', '/committees/{path}/tiers/{tier}'], ['delete', '/committees/{path}/tiers/{tier}'],
    ['put', '/committees/{path}/tiers/{tier}/move/down'], ['put', '/committees/members/{user}'], ['delete', '/committees/members/{user}'],
]);

it('writes an audit entry for every change to paths and levels', function () {
    ['product' => $product] = committeeFixture();
    AuditLog::query()->getQuery()->delete();

    $this->post(route('committees.store'), ['product_id' => $product->id, 'condition' => 'NEW', 'mechanism' => 'plafon', 'is_active' => true]);
    $path = CommitteePath::query()->where('condition', 'NEW')->firstOrFail();
    $this->put(route('committees.update', $path), ['product_id' => $product->id, 'condition' => 'NEW', 'mechanism' => 'plafon', 'is_active' => false]);
    $this->post(route('committees.tiers.store', $path), tierInput());
    $tier = $path->tiers()->firstOrFail();
    $this->put(route('committees.tiers.update', [$path, $tier]), tierInput(['label' => 'Baru']));
    $this->delete(route('committees.tiers.destroy', [$path, $tier]));
    $this->delete(route('committees.destroy', $path));

    expect(AuditLog::query()->orderBy('id')->pluck('event')->all())->toBe([
        'committee_paths.created', 'committee_paths.updated', 'committee_tiers.created', 'committee_tiers.updated', 'committee_tiers.deleted', 'committee_paths.deleted',
    ]);
});

it('never puts a national ID in the audit log when a member NIK is saved or removed', function () {
    committeeFixture();
    $member = User::factory()->create()->assignRole('Direktur Bisnis');

    $this->put(route('committees.members.update', $member), ['nik' => '3201010101010001'])->assertSessionHasNoErrors();
    $this->delete(route('committees.members.destroy', $member));

    expect(AuditLog::query()->where('new_values', 'like', '%3201010101010001%')->orWhere('old_values', 'like', '%3201010101010001%')->orWhere('context', 'like', '%3201010101010001%')->count())->toBe(0)
        ->and(AuditLog::query()->where('event', 'users.nik_changed')->count())->toBe(2);
});

dataset('member sorts', function () {
    foreach (['name', 'username', 'role', 'office', 'nik'] as $key) {
        foreach (['asc', 'desc'] as $direction) {
            yield "$key $direction" => [$key, $direction];
        }
    }
});

it('sorts the members by every column in both directions', function (string $key, string $direction) {
    committeeFixture();
    $office = Office::create(['code' => '00', 'alias' => 'PMK', 'name' => 'Pamanukan']);
    $rows = [['Bani', 'zeta', 'Direktur Utama', null], ['Citra', 'alpha', 'Direktur Bisnis', 'codex'], ['Adi', 'mike', 'Kepala Bagian Analis', 'manual']];

    foreach ($rows as [$name, $username, $role, $source]) {
        $user = User::factory()->create(['name' => $name, 'username' => $username, 'office_id' => $office->id])->assignRole($role);
        $source !== null && $user->setNik(fake()->numerify('################'), $source);
    }

    $order = $this->get(route('committees.members', ['sort' => $key, 'direction' => $direction, 'per_page' => 50]))
        ->assertOk()->inertiaProps('members.data');
    $names = collect($order)->pluck('name')->all();
    $expected = [
        'name' => ['Adi', 'Bani', 'Citra'], 'username' => ['Citra', 'Adi', 'Bani'], 'role' => ['Citra', 'Bani', 'Adi'], 'office' => ['Adi', 'Bani', 'Citra'], 'nik' => ['Bani', 'Citra', 'Adi'],
    ][$key];

    // Everyone shares one office, so that order stays by name (ties are always broken by name, ascending).
    expect($names)->toBe($direction === 'asc' || $key === 'office' ? $expected : array_reverse($expected));
})->with('member sorts');

it('refuses a sort key that is not a column of the members list', function (string $key) {
    $this->get(route('committees.members', ['sort' => $key]))->assertSessionHasErrors('sort');
})->with(['password', 'id', 'nik_hash']);

it('hands every tab of Data Komite the options of the authority check, and every tab page draws its button', function (string $route, string $page) {
    $this->get(route($route))->assertInertia(fn ($inertia) => $inertia->has('authority.productOptions')->has('authority.conditionMap')->has('authority.committeeMembers'));

    expect(file_get_contents(resource_path("js/pages/committees/{$page}.tsx")))->toContain('<AuthorityCheckButton options={authority} />');
})->with([['committees.levels', 'levels'], ['committees.special-rules', 'special-rules'], ['committees.members', 'members'], ['committees.exceptions', 'exceptions']]);

it('keeps the notes of the seeded paths in Indonesian, also when a database still holds the old English ones', function () {
    ['default' => $default] = committeeFixture();
    $default->update(['note' => 'Authority levels shared by every path that follows the defaults.']);
    CommitteePath::query()->where('is_default', false)->update(['note' => 'Loans for employees.']);

    $this->seed(CommitteeSeeder::class);

    $notes = CommitteePath::query()->whereNotNull('note')->where('condition', '!=', 'EXTRA')->pluck('note')->unique()->all();

    expect($default->fresh()->note)->toBe('Jenjang wewenang yang dipakai bersama oleh semua jalur yang mengikuti bawaan.')
        ->and(collect($notes)->filter(fn (string $note): bool => preg_match('/\b(Authority|Actual|Loans|Customers|Applies)\b/', $note) === 1)->all())->toBe([]);
});
