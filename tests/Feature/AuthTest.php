<?php

use App\Audit\AuditLog;
use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use App\Models\Office;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
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

it('leaves the stored person untouched when mirroring fails halfway', function () {
    User::factory()->create(['id' => 30, 'username' => '309011221', 'name' => 'Old Name'])->delete();
    User::restored(fn () => throw new RuntimeException('restore failed'));
    fakeCodex();

    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x'])->assertServerError();

    $user = User::withTrashed()->findOrFail(30);
    expect($user->name)->toBe('Old Name')->and($user->trashed())->toBeTrue()->and($user->office_id)->toBeNull()
        ->and(Office::count())->toBe(0);
    $this->assertGuest();
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
        ->assertSessionHasErrors(['username' => 'Layanan masuk tidak dapat dihubungi saat ini. Coba lagi sebentar lagi.']);
    $this->assertGuest();
});

it('treats an answer without a usable id or username as the service being down, storing nobody', function (array $user) {
    Http::fake(['codex.test/*' => Http::response(['success' => true, 'data' => ['user' => $user]])]);

    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x'])
        ->assertSessionHasErrors(['username' => 'Layanan masuk tidak dapat dihubungi saat ini. Coba lagi sebentar lagi.']);
    $this->assertGuest();
    expect(User::withTrashed()->count())->toBe(0);
})->with([
    'no id' => [['username' => '309011221', 'is_active' => true]],
    'zero id' => [['id' => 0, 'username' => '309011221', 'is_active' => true]],
    'no username' => [['id' => 30, 'is_active' => true]],
    'empty username' => [['id' => 30, 'username' => ' ', 'is_active' => true]],
]);

it('refuses an overlong password before calling Codex', function () {
    Http::fake();

    $this->post(route('login.store'), ['username' => '309011221', 'password' => str_repeat('x', 256)])->assertSessionHasErrors('password');
    Http::assertNothingSent();
});

it('accepts an id that Codex sends as a number in a string', function () {
    fakeCodex(['id' => '30']);

    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x'])->assertRedirect(route('dashboard'));

    expect(User::findOrFail(30)->username)->toBe('309011221');
});

it('treats an answer with fields of the wrong type as the service being down, storing nobody', function (array $user) {
    fakeCodex($user);

    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x'])
        ->assertSessionHasErrors(['username' => 'Layanan masuk tidak dapat dihubungi saat ini. Coba lagi sebentar lagi.']);
    $this->assertGuest();
    expect(User::withTrashed()->count())->toBe(0)->and(Office::count())->toBe(0);
})->with([
    'name as a list' => [['name' => ['A', 'B']]],
    'email as a list' => [['email' => ['a@b.test']]],
    'role as a list' => [['role' => ['Super Admin']]],
    'office name as a list' => [['office' => ['Pamanukan']]],
    'username as a number' => [['username' => 309011221]],
]);

it('does not follow a redirect from Codex and says the service is down', function () {
    Http::fake([
        'codex.test/*' => Http::response('', 302, ['Location' => 'https://elsewhere.test/api/web-auth']),
        'elsewhere.test/*' => Http::response(['success' => true]),
    ]);

    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x'])
        ->assertSessionHasErrors(['username' => 'Layanan masuk tidak dapat dihubungi saat ini. Coba lagi sebentar lagi.']);
    Http::assertSentCount(1);
    $this->assertGuest();
});

it('says the service is down when the Codex address is not configured', function () {
    config(['services.codex.endpoint' => '']);
    Http::preventStrayRequests();

    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x'])
        ->assertSessionHasErrors(['username' => 'Layanan masuk tidak dapat dihubungi saat ini. Coba lagi sebentar lagi.']);
    $this->assertGuest();
});

it('goes back to the page asked for before signing in, and ignores a destination sent with the form', function () {
    fakeCodex(['role' => 'Super Admin']);

    $this->get('/collaterals')->assertRedirect(route('login'));
    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x', 'intended' => 'https://evil.test', 'redirect' => 'https://evil.test'])
        ->assertRedirect(url('/collaterals'));

    $this->post(route('logout'));
    fakeCodex(['role' => 'Super Admin']);
    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x', 'intended' => 'https://evil.test'])->assertRedirect(route('dashboard'));
});

it('counts the attempt before asking Codex, so parallel requests cannot slip under the limit', function () {
    $key = Str::transliterate('309011221|127.0.0.1');
    $during = null;
    Http::fake(['codex.test/*' => function () use (&$during, $key) {
        $during = RateLimiter::attempts($key);

        return Http::response(['success' => false], 401);
    }]);

    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'bad']);

    expect($during)->toBe(1)->and(RateLimiter::attempts($key))->toBe(1);
});

it('does not count attempts that failed because Codex was down, and a success clears the count', function () {
    foreach (range(1, 6) as $i) {
        Http::swap(new Factory);
        Http::fake(['codex.test/*' => Http::response('down', 500)]);
        $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x'])
            ->assertSessionHasErrors(['username' => 'Layanan masuk tidak dapat dihubungi saat ini. Coba lagi sebentar lagi.']);
    }

    expect(RateLimiter::attempts(Str::transliterate('309011221|127.0.0.1')))->toBe(0);

    fakeCodex();
    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x'])->assertRedirect(route('dashboard'));
});

it('shares one limit between a person\'s email and username', function () {
    User::factory()->create(['id' => 30, 'username' => '309011221', 'email' => 'person@example.com']);
    Http::fake(['codex.test/*' => Http::response(['success' => false], 401)]);

    foreach (['309011221', 'person@example.com', ' Person@Example.com ', '309011221', 'person@example.com'] as $typed) {
        $this->post(route('login.store'), ['username' => $typed, 'password' => 'bad']);
    }

    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'bad'])->assertSessionHasErrors('username');
    Http::assertSentCount(5);
});

it('writes one lockout entry per lockout, not one per blocked request', function () {
    Http::fake(['codex.test/*' => Http::response(['success' => false], 401)]);

    foreach (range(1, 12) as $i) {
        $this->post(route('login.store'), ['username' => 'someone', 'password' => 'bad']);
    }

    expect(AuditLog::query()->where('event', 'auth.locked_out')->count())->toBe(1)
        ->and(AuditLog::query()->where('event', 'auth.login_failed')->where('outcome', 'failure')->count())->toBe(5);
});

it('writes exactly one entry for each outcome of a sign-in and never the password', function (callable $arrange, string $event, string $reason) {
    $arrange();

    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'S3cret-typed']);

    $rows = AuditLog::query()->where('module', 'auth')->get();
    expect($rows->pluck('event')->all())->toBe([$event]);

    if ($reason !== '') {
        expect($rows->first()->decoded('context')['reason'])->toBe($reason);
    }

    expect(AuditLog::query()->get()->map(fn ($row) => json_encode($row->getAttributes()))->implode(''))->not->toContain('S3cret-typed');
})->with([
    'wrong password' => [fn () => Http::fake(['codex.test/*' => Http::response(['success' => false], 401)]), 'auth.login_failed', 'invalid credentials'],
    'Codex down' => [fn () => Http::fake(['codex.test/*' => Http::response('down', 500)]), 'auth.login_failed', 'sign-in service unavailable'],
    'unusable answer' => [fn () => fakeCodex(['id' => 0]), 'auth.login_failed', 'sign-in service unavailable'],
    'inactive person' => [fn () => fakeCodex(['is_active' => false]), 'auth.login_failed', 'inactive account'],
    'success' => [fn () => fakeCodex(), 'auth.login', ''],
]);

it('records the person created at the first sign-in as a Codex change and writes nothing more at the next one', function () {
    fakeCodex();
    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x']);
    $this->post(route('logout'));

    $created = AuditLog::query()->where('event', 'users.created')->get();
    expect($created)->toHaveCount(1)->and($created->first()->decoded('context')['source'] ?? null)->toBe('codex');

    $before = AuditLog::query()->count();
    fakeCodex();
    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x']);

    // Only the sign-in itself: the mirror found nothing to change.
    expect(AuditLog::query()->count())->toBe($before + 1)->and(AuditLog::query()->latest('id')->value('event'))->toBe('auth.login');
});

it('gives the person a new session id at sign-in and ends the session at sign-out', function () {
    fakeCodex();
    $this->get(route('login'));
    $before = session()->getId();

    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x']);
    expect(session()->getId())->not->toBe($before);

    $signedIn = session()->getId();
    $this->post(route('logout'));
    expect(session()->getId())->not->toBe($signedIn);
    $this->assertGuest();
});

it('sends a person who Codex switched off to the login page on the next request', function () {
    fakeCodex(['role' => 'Super Admin']);
    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x']);
    $this->get(route('dashboard'))->assertOk();

    User::findOrFail(30)->delete();
    auth()->forgetGuards(); // a real request starts with no user in memory

    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

it('creates a person once when two first sign-ins collide', function () {
    $collided = false;
    User::creating(function () use (&$collided) {
        if (! $collided) {
            $collided = true;
            DB::table('users')->insert(['id' => 30, 'username' => '309011221', 'name' => 'Other request', 'email' => 'other@example.com', 'password' => 'x', 'created_at' => now(), 'updated_at' => now()]);
        }
    });
    fakeCodex();

    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x'])->assertRedirect(route('dashboard'));

    expect(User::withTrashed()->where('username', '309011221')->count())->toBe(1)->and(User::findOrFail(30)->name)->toBe('Test Person');
});

it('refuses with a clear message a person whose records cannot follow them to a new Codex id', function () {
    $old = User::factory()->create(['id' => 99, 'username' => '309011221']);
    $loan = LoanApplication::create([
        'application_code' => LoanApplication::nextCode(), 'application_date' => now(), 'status' => LoanStatus::Draft,
        'nik' => '3201000000000001', 'full_name' => 'Siti', 'created_by' => $old->id,
    ]);
    fakeCodex();

    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x'])
        ->assertSessionHasErrors(['username' => 'Data Anda di Codex tidak cocok dengan data di SIPEBRI. Hubungi Super Admin.']);

    $this->assertGuest();
    expect(User::find(99))->not->toBeNull()->and(User::find(30))->toBeNull()->and($loan->fresh()->created_by)->toBe(99)
        ->and(AuditLog::query()->where('event', 'auth.login_failed')->first()->decoded('context')['reason'])->toBe('user id conflict')
        ->and(RateLimiter::attempts(Str::transliterate('309011221|127.0.0.1')))->toBe(0);
});

it('gives a placeholder email to a person whose Codex email another person already holds', function () {
    User::factory()->create(['id' => 7, 'email' => 'person@example.com']);
    fakeCodex();

    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x'])->assertRedirect(route('dashboard'));

    expect(User::findOrFail(30)->email)->toBe('309011221@codex.invalid')->and(User::findOrFail(7)->email)->toBe('person@example.com');
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

it('lands people on the first screen they may open, or explains when there is none', function () {
    $this->actingAs(userWith(['collaterals.view'], 'Collateral Viewer'))->get(route('dashboard'))->assertRedirect('/collaterals');
    $this->actingAs(userWith([], 'Guest Like'))->get(route('dashboard'))->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('no-access')->where('auth.user.role', 'Guest Like'));
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

it('tells search engines not to index any page', function () {
    $this->get(route('login'))->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    $this->get('/no-such-page')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
});
