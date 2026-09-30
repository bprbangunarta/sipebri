<?php

use App\Mail\LoginCode;
use App\Models\User;
use App\Security\TwoFactor\RecoveryCodes;
use App\Security\TwoFactor\Totp;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    config([
        'services.codex.endpoint' => 'https://codex.test', 'services.codex.token' => 'test-token',
        'security.mfa_enabled' => true, 'security.email_resend_seconds' => 60,
    ]);
    $this->seed(RoleSeeder::class);
    Mail::fake();
    Cache::flush();
    RateLimiter::clear('two-factor-login:30');
});

/**
 * @param  array<string, mixed>  $attributes
 */
function mfaUser(string $method, array $attributes = []): User
{
    $secret = Totp::generateSecret();

    return User::factory()->create([
        'id' => 30, 'username' => '309011221', 'email' => 'person@example.com',
        ...$attributes,
    ])->forceFill([
        'mfa_method' => $method, 'mfa_secret' => $method === 'totp' ? $secret : null, 'mfa_confirmed_at' => now(),
    ]);
}

function lastEmailedCode(): string
{
    $code = null;
    Mail::assertSent(LoginCode::class, function (LoginCode $mail) use (&$code) {
        $code = $mail->code;

        return true;
    });

    return $code;
}

function signInStep(): void
{
    test()->post(route('login.store'), ['username' => '309011221', 'password' => 'x']);
}

it('matches the RFC 6238 test vector', function () {
    expect(Totp::code('GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ', 59))->toBe('287082')
        ->and(Totp::verify('GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ', '287082', 1, 59))->not->toBeNull()
        ->and(Totp::verify('GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ', '287082', 1, 59 + 300))->toBeNull()
        ->and(Totp::verify('GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ', 'abcdef'))->toBeNull();
});

it('signs in straight away when the person has no second factor', function () {
    fakeCodex();
    User::factory()->create(['id' => 30, 'username' => '309011221']);

    signInStep();

    $this->assertAuthenticated();
    Mail::assertNothingSent();
});

it('challenges a person with the email method and only signs them in after the right code', function () {
    mfaUser('email')->save();
    fakeCodex();

    signInStep();

    $this->assertGuest();
    expect(session('two_factor.login.id'))->toBe(30);
    Mail::assertSent(LoginCode::class, fn (LoginCode $m) => $m->hasTo('person@example.com'));
    $this->get(route('two-factor.challenge'))->assertInertia(fn (Assert $p) => $p->component('auth/two-factor-challenge')->where('method', 'email')->where('email', 'pe****@example.com'));

    $this->post(route('two-factor.verify'), ['code' => '000000'])->assertSessionHasErrors('code');
    $this->assertGuest();

    $this->post(route('two-factor.verify'), ['code' => lastEmailedCode()])->assertRedirect(route('home'));
    $this->assertAuthenticatedAs(User::find(30));
    expect(session('two_factor.login'))->toBeNull();
});

it('challenges a person with an authenticator app and refuses a replayed code', function () {
    $user = mfaUser('totp');
    $user->save();
    fakeCodex();

    signInStep();
    $this->assertGuest();
    Mail::assertNothingSent();

    $code = Totp::code($user->mfa_secret);
    $this->post(route('two-factor.verify'), ['code' => $code])->assertRedirect(route('home'));
    $this->assertAuthenticated();

    $this->post(route('logout'));
    signInStep();
    $this->post(route('two-factor.verify'), ['code' => $code])->assertSessionHasErrors('code');
    $this->assertGuest();
});

it('accepts a recovery code once', function () {
    $user = mfaUser('totp');
    $user->save();
    $codes = app(RecoveryCodes::class)->regenerate($user);
    fakeCodex();

    signInStep();
    $this->post(route('two-factor.verify'), ['code' => $codes[0]])->assertRedirect(route('home'));

    $this->post(route('logout'));
    signInStep();
    $this->post(route('two-factor.verify'), ['code' => $codes[0]])->assertSessionHasErrors('code');
    expect(app(RecoveryCodes::class)->remaining($user->fresh()))->toBe(7);
});

it('never reaches the second step for an inactive person', function () {
    mfaUser('email')->save();
    fakeCodex(['is_active' => false]);

    signInStep();

    $this->assertGuest();
    expect(session('two_factor.login'))->toBeNull();
    Mail::assertNothingSent();
});

it('does nothing when the feature is switched off', function () {
    config(['security.mfa_enabled' => false]);
    mfaUser('email')->save();
    fakeCodex();

    signInStep();

    $this->assertAuthenticated();
    Mail::assertNothingSent();
    $this->get(route('profile.show'))->assertInertia(fn (Assert $p) => $p->where('twoFactor.enabled', false));
    $this->post(route('profile.two-factor.totp.start'))->assertNotFound();
});

it('drops the attempt after too many wrong codes and after it expires', function () {
    mfaUser('email')->save();
    fakeCodex();
    signInStep();

    foreach (range(1, 5) as $i) {
        $this->post(route('two-factor.verify'), ['code' => '111111']);
    }
    $this->post(route('two-factor.verify'), ['code' => lastEmailedCode()])->assertRedirect(route('login'));
    $this->assertGuest();

    RateLimiter::clear('two-factor-login:30');
    signInStep();
    $this->travel(11)->minutes();
    $this->post(route('two-factor.verify'), ['code' => '123456'])->assertRedirect(route('login'));
    $this->assertGuest();
});

it('resends the email code only after the cooldown and can be cancelled', function () {
    mfaUser('email')->save();
    fakeCodex();
    signInStep();

    $this->post(route('two-factor.resend'))->assertSessionHas('error');
    Mail::assertSentCount(1);

    $this->travel(61)->seconds();
    $this->post(route('two-factor.resend'))->assertSessionHas('success');
    Mail::assertSentCount(2);

    $this->post(route('two-factor.cancel'))->assertRedirect(route('login'));
    expect(session('two_factor.login'))->toBeNull();
});

it('lets a person turn on email codes after confirming the address, and turn them off with a code', function () {
    $user = User::factory()->create(['email' => 'person@example.com']);
    $this->actingAs($user);

    $this->post(route('profile.two-factor.email.send'))->assertSessionHas('success');
    $this->post(route('profile.two-factor.email.enable'), ['code' => '000000'])->assertSessionHasErrors('code');
    expect($user->fresh()->mfa_method)->toBeNull();

    $this->post(route('profile.two-factor.email.enable'), ['code' => lastEmailedCode()])->assertSessionHasNoErrors();
    expect($user->fresh()->only(['mfa_method']))->toBe(['mfa_method' => 'email'])->and($user->fresh()->mfa_confirmed_at)->not->toBeNull();
    $this->get(route('dashboard'))->assertInertia(fn (Assert $p) => $p->where('security.method', 'email'));

    $this->travel(61)->seconds();
    $this->post(route('profile.two-factor.email.send'));
    $this->delete(route('profile.two-factor.disable'), ['code' => '000000'])->assertSessionHasErrors('code');
    $this->delete(route('profile.two-factor.disable'), ['code' => lastEmailedCode()])->assertSessionHasNoErrors();
    expect($user->fresh()->mfa_method)->toBeNull()->and($user->fresh()->mfa_secret)->toBeNull();
});

it('lets a person turn on an authenticator app and shows recovery codes once', function () {
    $user = User::factory()->create(['username' => '309011221']);
    $this->actingAs($user);

    $this->post(route('profile.two-factor.totp.start'));
    $secret = session('two_factor.setup_secret');
    expect($secret)->toBeString();
    $this->get(route('profile.show'))->assertInertia(fn (Assert $p) => $p->where('twoFactor.setup.secret', $secret)->where('twoFactor.setup.uri', fn ($uri) => str_starts_with($uri, 'otpauth://totp/')));

    $this->post(route('profile.two-factor.totp.enable'), ['code' => '000000'])->assertSessionHasErrors('code');
    $this->post(route('profile.two-factor.totp.enable'), ['code' => Totp::code($secret)])->assertSessionHasNoErrors();

    $fresh = $user->fresh();
    expect($fresh->mfa_method)->toBe('totp')->and($fresh->mfa_secret)->toBe($secret)->and($fresh->mfa_recovery_codes)->toHaveCount(8);
    $this->get(route('profile.show'))->assertInertia(fn (Assert $p) => $p->has('twoFactor.recoveryCodes', 8)->where('twoFactor.method', 'totp'));
    $this->get(route('profile.show'))->assertInertia(fn (Assert $p) => $p->where('twoFactor.recoveryCodes', null));
    expect(DB::table('users')->where('id', $user->id)->value('mfa_secret'))->not->toBe($secret);
});

it('does not allow email codes for an address that cannot receive them', function () {
    $this->actingAs(User::factory()->create(['email' => 'nobody@codex.invalid']));

    $this->post(route('profile.two-factor.email.send'))->assertStatus(422);
    $this->get(route('profile.show'))->assertInertia(fn (Assert $p) => $p->where('twoFactor.emailAvailable', false));
});

it('shares the two-factor state used by the reminder banner', function () {
    $this->actingAs(superAdmin())->get(route('dashboard'))->assertInertia(fn (Assert $p) => $p->where('security', ['enabled' => true, 'method' => null]));
});

it('changes the password through Codex and maps its answers', function () {
    $user = User::factory()->create(['username' => '309011221']);
    $this->actingAs($user);
    $payload = ['current_password' => 'old-pass-1', 'password' => 'new-pass-22', 'password_confirmation' => 'new-pass-22'];

    Http::swap(new Factory);
    Http::fake(['codex.test/api/web-auth/password' => Http::response(['success' => true, 'message' => 'Kata sandi berhasil diubah'])]);
    $this->put(route('profile.password'), $payload)->assertSessionHasNoErrors()->assertSessionHas('success', 'Kata sandi berhasil diubah');
    Http::assertSent(fn ($r) => $r->url() === 'https://codex.test/api/web-auth/password' && $r['username'] === '309011221' && $r['current_password'] === 'old-pass-1'
        && $r['password'] === 'new-pass-22' && $r['confirmed_password'] === 'new-pass-22' && $r->hasHeader('Authorization', 'Bearer test-token'));

    Http::swap(new Factory);
    Http::fake(['codex.test/*' => Http::response(['success' => false, 'message' => 'Kata sandi saat ini salah'], 422)]);
    $this->put(route('profile.password'), $payload)->assertSessionHasErrors(['current_password' => 'Kata sandi saat ini salah']);

    Http::swap(new Factory);
    Http::fake(['codex.test/*' => Http::response('down', 500)]);
    $this->put(route('profile.password'), $payload)->assertSessionHasErrors('current_password');
});

it('validates the new password before calling Codex', function () {
    $this->actingAs(User::factory()->create(['username' => '309011221']));
    Http::swap(new Factory);
    Http::fake();

    $this->put(route('profile.password'), ['current_password' => 'abc', 'password' => 'short', 'password_confirmation' => 'short'])->assertSessionHasErrors('password');
    $this->put(route('profile.password'), ['current_password' => 'abc12345', 'password' => 'abc12345', 'password_confirmation' => 'abc12345'])->assertSessionHasErrors('password');
    $this->put(route('profile.password'), ['current_password' => 'abc', 'password' => 'long-enough-1', 'password_confirmation' => 'different-1'])->assertSessionHasErrors('password');
    Http::assertNothingSent();
});
