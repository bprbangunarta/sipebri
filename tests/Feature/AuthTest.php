<?php

use App\Models\Office;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    config(['services.codex.endpoint' => 'https://codex.test', 'services.codex.token' => 'test-token']);
    $this->seed(RoleSeeder::class);
});

it('redirects guests to the login page', function (string $uri) {
    $this->get($uri)->assertRedirect(route('login'));
})->with(['/', '/loan-applications', '/collaterals', '/committees', '/references/regions']);

it('renders the login page', function () {
    $this->get(route('login'))->assertOk();
});

it('signs in an active person and mirrors them locally with role and office', function () {
    fakeCodex();

    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'anything'])->assertRedirect(route('dashboard'));

    $user = User::where('username', '309011221')->firstOrFail();
    $this->assertAuthenticatedAs($user);
    expect($user->id)->toBe(30)
        ->and($user->only(['name', 'email']))->toBe(['name' => 'Test Person', 'email' => 'person@example.com'])
        ->and($user->office->code)->toBe('00')
        ->and($user->hasRole('Kepala Seksi Analis'))->toBeTrue();
    Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer test-token')
        && $request->url() === 'https://codex.test/api/web-auth' && $request['username'] === '309011221' && $request['password'] === 'anything');
});

it('creates the office from Codex and refreshes it on the next sign-in, ignoring the extra office data', function () {
    fakeCodex();
    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x']);

    $office = Office::where('code', '00')->firstOrFail();
    expect($office->name)->toBe('Pamanukan')->and($office->alias)->toBe('PMK')
        ->and(User::findOrFail(30)->office_id)->toBe($office->id);

    $this->post(route('logout'));
    Http::swap(new Factory);
    Http::fake(['codex.test/*' => Http::response(['success' => true, 'data' => [
        'user' => ['id' => 30, 'username' => '309011221', 'name' => 'Test Person', 'role' => 'Guest', 'is_active' => true],
        'office' => ['code' => '00', 'name' => 'Pamanukan Baru', 'address' => 'New street', 'telephone' => '021', 'latitude' => '-6.286353', 'longitude' => '107.820866', 'radius' => '50', 'coa' => '1.100.2.1'],
    ]])]);
    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x']);

    $office->refresh();
    expect(Office::count())->toBe(1)->and($office->alias)->toBe('PMK')->and($office->name)->toBe('Pamanukan Baru');
});

it('falls back to the office code as alias, and never steals an alias another office holds', function () {
    Office::create(['code' => '01', 'alias' => 'DUP', 'name' => 'Other']);
    Http::swap(new Factory);
    Http::fake(['codex.test/*' => Http::response(['success' => true, 'data' => [
        'user' => ['id' => 40, 'username' => 'u40', 'name' => 'U', 'role' => 'Guest', 'is_active' => true],
        'office' => ['code' => '07', 'name' => 'No Alias Office'],
    ]])]);
    $this->post(route('login.store'), ['username' => 'u40', 'password' => 'x']);
    expect(Office::firstWhere('code', '07')->alias)->toBe('07');

    $this->post(route('logout'));
    Http::swap(new Factory);
    Http::fake(['codex.test/*' => Http::response(['success' => true, 'data' => [
        'user' => ['id' => 41, 'username' => 'u41', 'name' => 'V', 'role' => 'Guest', 'is_active' => true],
        'office' => ['code' => '08', 'alias' => 'dup', 'name' => 'Clash Office'],
    ]])]);
    $this->post(route('login.store'), ['username' => 'u41', 'password' => 'x']);
    expect(Office::firstWhere('code', '08')->alias)->toBeNull()->and(Office::firstWhere('code', '01')->alias)->toBe('DUP');
});

it('leaves the office empty when Codex sends none and refuses to delete an office people belong to', function () {
    Http::swap(new Factory);
    Http::fake(['codex.test/*' => Http::response(['success' => true, 'data' => ['user' => ['id' => 31, 'username' => 'nobody', 'name' => 'No Office', 'role' => 'Guest', 'is_active' => true]]])]);
    $this->post(route('login.store'), ['username' => 'nobody', 'password' => 'x']);
    expect(User::findOrFail(31)->office_id)->toBeNull();

    $office = Office::create(['code' => '09', 'alias' => 'XYZ', 'name' => 'Somewhere']);
    User::findOrFail(31)->update(['office_id' => $office->id]);
    $this->actingAs(superAdmin())->delete(route('references.destroy', ['offices', $office->id]))->assertSessionHas('error');
    expect(Office::find($office->id))->not->toBeNull();
});

it('updates an existing person and lets the role follow Codex', function () {
    User::factory()->create(['id' => 30, 'username' => '309011221', 'name' => 'Old Name'])->assignRole('AO Kredit');
    fakeCodex(['name' => 'New Name', 'role' => 'Kepala Seksi Analis']);

    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x'])->assertRedirect(route('dashboard'));

    $user = User::findOrFail(30);
    expect(User::where('username', '309011221')->count())->toBe(1)->and($user->name)->toBe('New Name')
        ->and($user->hasRole('Kepala Seksi Analis'))->toBeTrue()->and($user->hasRole('AO Kredit'))->toBeFalse();
});

it('moves a person stored under another id to the Codex id', function () {
    $old = User::factory()->create(['id' => 99, 'username' => '309011221'])->assignRole('AO Kredit');
    fakeCodex(['role' => 'AO Kredit']);

    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x'])->assertRedirect(route('dashboard'));

    expect(User::withTrashed()->find(99))->toBeNull()->and(User::find(30)->hasRole('AO Kredit'))->toBeTrue();
});

it('refuses an inactive person but keeps them stored as soft deleted', function () {
    fakeCodex(['is_active' => false]);

    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x'])->assertSessionHasErrors('username');

    $this->assertGuest();
    expect(User::withTrashed()->findOrFail(30)->trashed())->toBeTrue();
});

it('restores a soft-deleted person when Codex says active again, and soft-deletes one turned inactive', function () {
    User::factory()->create(['id' => 30, 'username' => '309011221'])->delete();
    fakeCodex();
    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x'])->assertRedirect(route('dashboard'));
    expect(User::withTrashed()->findOrFail(30)->trashed())->toBeFalse();

    $this->post(route('logout'));
    fakeCodex(['is_active' => false]);
    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x'])->assertSessionHasErrors('username');
    expect(User::withTrashed()->findOrFail(30)->trashed())->toBeTrue();
});

it('refuses wrong credentials without creating anybody', function () {
    Http::fake(['codex.test/*' => Http::response(['success' => false, 'message' => 'Wrong'], 401)]);

    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'wrong'])->assertSessionHasErrors('username');

    $this->assertGuest();
    expect(User::count())->toBe(0);
});

it('shows a clear message when Codex is unreachable', function () {
    Http::fake(['codex.test/*' => Http::response('down', 500)]);

    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x'])
        ->assertSessionHasErrors(['username' => 'The sign-in service cannot be reached right now. Please try again shortly.']);
    $this->assertGuest();
});

it('validates the form before calling Codex', function () {
    Http::fake();

    $this->post(route('login.store'), ['username' => '', 'password' => ''])->assertSessionHasErrors(['username', 'password']);
    Http::assertNothingSent();
});

it('makes a person with an unknown role a Guest', function () {
    fakeCodex(['role' => 'Some Unknown Role']);

    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x'])->assertRedirect(route('dashboard'));

    $user = User::findOrFail(30);
    expect($user->hasRole('Guest'))->toBeTrue()->and($user->roles)->toHaveCount(1);
});

it('gives Super Admin to a person whose Codex role is Super Admin', function () {
    fakeCodex(['role' => 'Super Admin']);

    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x'])->assertRedirect(route('dashboard'));

    $user = User::findOrFail(30);
    expect($user->hasRole('Super Admin'))->toBeTrue()->and($user->roles)->toHaveCount(1)->and($user->can('surveys.manage'))->toBeTrue();
});

it('keeps the Codex id when the person is created, including the soft delete state', function () {
    fakeCodex(['id' => 4711, 'is_active' => false]);

    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x'])->assertSessionHasErrors('username');

    $stored = User::withTrashed()->findOrFail(4711);
    expect($stored->username)->toBe('309011221')->and($stored->trashed())->toBeTrue();
});

it('translates a known email into the Codex username', function () {
    User::factory()->create(['id' => 30, 'username' => '309011221', 'email' => 'person@example.com']);
    fakeCodex();

    $this->post(route('login.store'), ['username' => 'Person@Example.com', 'password' => 'x'])->assertRedirect(route('dashboard'));

    Http::assertSent(fn ($request) => $request['username'] === '309011221');
    expect(User::where('username', '309011221')->count())->toBe(1);
});

it('locks out after repeated wrong attempts', function () {
    Http::fake(['codex.test/*' => Http::response(['success' => false], 401)]);

    foreach (range(1, 5) as $i) {
        $this->post(route('login.store'), ['username' => 'someone', 'password' => 'bad']);
    }

    $this->post(route('login.store'), ['username' => 'someone', 'password' => 'bad'])->assertSessionHasErrors('username');
    Http::assertSentCount(5);
});

it('stops a soft-deleted person\'s existing session', function () {
    $user = User::factory()->create();
    $provider = auth()->guard('web')->getProvider();
    expect($provider->retrieveById($user->id))->not->toBeNull();

    $user->delete();

    expect($provider->retrieveById($user->id))->toBeNull();
});

it('lands people on the first screen they may open, or refuses when there is none', function () {
    $this->actingAs(userWith(['collaterals.view'], 'Collateral Viewer'))->get(route('dashboard'))->assertRedirect('/collaterals');
    $this->actingAs(userWith([], 'Guest Like'))->get(route('dashboard'))->assertForbidden()
        ->assertInertia(fn (Assert $page) => $page->component('error')->where('status', 403)->where('auth.user.role', 'Guest Like'));
});

it('logs out', function () {
    $this->actingAs(superAdmin())->post(route('logout'))->assertRedirect(route('login'));

    $this->assertGuest();
});

it('redirects signed-in users away from the login page', function () {
    $this->actingAs(superAdmin())->get(route('login'))->assertRedirect(route('dashboard'));
});

it('renders the error page for a missing route, even for guests, without needing a layout', function () {
    $this->get('/no-such-page')->assertNotFound()->assertInertia(fn (Assert $page) => $page->component('error')->where('status', 404));
});

it('renders themed Blade error pages for statuses the app layout cannot cover', function (int $status, string $title) {
    Route::get('/_error-test/'.$status, fn () => abort($status));

    $this->get('/_error-test/'.$status)->assertStatus($status)->assertSee($title)->assertSee(config('app.name'))->assertSee('bg-primary', false);
})->with([[500, 'Terjadi kesalahan'], [503, 'Sedang dalam pemeliharaan'], [429, 'Terlalu banyak permintaan'], [401, 'Perlu masuk']]);
