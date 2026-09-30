<?php

namespace App\Http\Controllers;

use App\Audit\Audit;
use App\Auth\CodexAuthClient;
use App\Auth\CodexUnavailable;
use App\Models\User;
use App\Security\TwoFactor\EmailCode;
use App\Security\TwoFactor\RecoveryCodes;
use App\Security\TwoFactor\Totp;
use App\Security\TwoFactor\TwoFactor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * The signed-in person's own profile. The account itself comes from Codex (read-only here, and so is the
 * password); what a person controls is their two-factor authentication.
 */
class ProfileController extends Controller
{
    public function __construct(private TwoFactor $twoFactor, private EmailCode $email, private RecoveryCodes $recovery) {}

    public function show(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user()->load('office:id,alias,name');
        $secret = $request->session()->get('two_factor.setup_secret');

        return Inertia::render('profile/show', [
            'account' => [
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'office' => $user->office ? trim(($user->office->alias ? $user->office->alias.' : ' : '').$user->office->name) : null,
                'role' => $user->getRoleNames()->first(),
            ],
            'twoFactor' => [
                'enabled' => $this->twoFactor->enabled(),
                'method' => $this->twoFactor->methodFor($user),
                'emailAvailable' => $this->email->canReceive($user),
                'recoveryRemaining' => $this->recovery->remaining($user),
                'setup' => $secret ? [
                    'secret' => $secret,
                    'uri' => Totp::uri($secret, $user->username ?? $user->email, (string) config('app.name')),
                ] : null,
                'emailCodeSent' => (bool) $request->session()->get('two_factor.email_sent'),
                'resendIn' => $this->email->secondsUntilResend($user),
                'recoveryCodes' => $request->session()->get('two_factor.recovery_codes'),
            ],
        ]);
    }

    /**
     * Change the password in Codex. The current password is verified by Codex, so a hijacked session
     * alone cannot set a new one. Attempts are limited per person.
     */
    public function updatePassword(Request $request, CodexAuthClient $codex): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $key = "password-change:{$user->id}";

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', Password::defaults(), 'min:8', 'confirmed', 'different:current_password'],
        ]);

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages(['current_password' => 'Too many attempts. Try again in '.ceil(RateLimiter::availableIn($key) / 60).' minutes.']);
        }

        try {
            $result = $codex->changePassword((string) $user->username, $data['current_password'], $data['password'], $request->string('password_confirmation')->toString());
        } catch (CodexUnavailable $exception) {
            report($exception);

            throw ValidationException::withMessages(['current_password' => 'The password service cannot be reached right now. Please try again shortly.']);
        }

        if (! $result['ok']) {
            RateLimiter::hit($key, 600);
            Audit::record('profile.password_change_failed', 'profile', 'password_change_failed', $user, outcome: 'failure');

            throw ValidationException::withMessages($result['errors'] !== [] ? $result['errors'] : ['current_password' => $result['message'] !== '' ? $result['message'] : 'The password could not be changed.']);
        }

        RateLimiter::clear($key);
        Audit::record('profile.password_changed', 'profile', 'password_changed', $user);

        return back()->with('success', $result['message'] !== '' ? $result['message'] : 'Your password was changed.');
    }

    /** Send a code to the person's own email: to confirm the email method, or to authorise turning two-factor off. */
    public function sendEmailCode(Request $request): RedirectResponse
    {
        $user = $this->guard($request);
        abort_unless($this->email->canReceive($user), 422, 'Your account has no email address that can receive codes.');

        try {
            $sent = $this->email->issue($user);
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', 'We could not send the code. Please try again shortly.');
        }

        if (! $sent) {
            return back()->with('error', "Please wait {$this->email->secondsUntilResend($user)} seconds before asking for another code.");
        }

        $request->session()->put('two_factor.email_sent', true);

        return back()->with('success', 'A code has been sent to your email.');
    }

    public function enableEmail(Request $request): RedirectResponse
    {
        $user = $this->guard($request);
        $this->throttle($user);

        if (! $this->email->verify($user, $this->code($request))) {
            RateLimiter::hit($this->throttleKey($user), 600);

            throw ValidationException::withMessages(['code' => 'The code is not valid. Check it or request a new one.']);
        }

        RateLimiter::clear($this->throttleKey($user));
        $this->store($user, TwoFactor::EMAIL, null);
        Audit::record('profile.mfa_enabled', 'profile', 'mfa_enabled', $user, context: ['method' => TwoFactor::EMAIL]);
        $request->session()->forget(['two_factor.email_sent', 'two_factor.setup_secret']);

        return back()->with('success', 'Two-factor authentication by email is on.');
    }

    /** Begin authenticator-app setup: create the pending secret that the QR code shows. */
    public function startTotp(Request $request): RedirectResponse
    {
        $this->guard($request);
        $request->session()->put('two_factor.setup_secret', Totp::generateSecret());

        return back();
    }

    public function cancelTotp(Request $request): RedirectResponse
    {
        $request->session()->forget('two_factor.setup_secret');

        return back();
    }

    public function enableTotp(Request $request): RedirectResponse
    {
        $user = $this->guard($request);
        $secret = $request->session()->get('two_factor.setup_secret');
        abort_if($secret === null, 422, 'Start the setup again.');
        $this->throttle($user);

        if (! $this->twoFactor->verifyTotp($user, $this->code($request), $secret)) {
            RateLimiter::hit($this->throttleKey($user), 600);

            throw ValidationException::withMessages(['code' => 'The code is not valid. Check that your phone clock is correct and try again.']);
        }

        RateLimiter::clear($this->throttleKey($user));
        $this->store($user, TwoFactor::TOTP, $secret);
        Audit::record('profile.mfa_enabled', 'profile', 'mfa_enabled', $user, context: ['method' => TwoFactor::TOTP]);
        $request->session()->forget(['two_factor.setup_secret', 'two_factor.email_sent']);
        $request->session()->flash('two_factor.recovery_codes', $this->recovery->regenerate($user));

        return back()->with('success', 'Two-factor authentication with an authenticator app is on. Save your recovery codes.');
    }

    /** Turn two-factor off. Needs a valid code of the current method, so a hijacked session cannot do it silently. */
    public function disable(Request $request): RedirectResponse
    {
        $user = $this->guard($request);
        $this->throttle($user);

        if (! $this->twoFactor->verify($user, $this->code($request))) {
            RateLimiter::hit($this->throttleKey($user), 600);

            throw ValidationException::withMessages(['code' => 'The code is not valid.']);
        }

        RateLimiter::clear($this->throttleKey($user));
        Audit::record('profile.mfa_disabled', 'profile', 'mfa_disabled', $user, context: ['method' => $user->mfa_method]);
        $user->forceFill(['mfa_method' => null, 'mfa_secret' => null, 'mfa_recovery_codes' => null, 'mfa_confirmed_at' => null])->save();
        $request->session()->forget(['two_factor.email_sent', 'two_factor.setup_secret']);

        return back()->with('success', 'Two-factor authentication is off.');
    }

    private function guard(Request $request): User
    {
        abort_unless($this->twoFactor->enabled(), 404);

        return $request->user();
    }

    private function code(Request $request): string
    {
        return $request->validate(['code' => ['required', 'string', 'max:32']])['code'];
    }

    private function store(User $user, string $method, ?string $secret): void
    {
        $user->forceFill(['mfa_method' => $method, 'mfa_secret' => $secret, 'mfa_recovery_codes' => null, 'mfa_confirmed_at' => now()])->save();
    }

    private function throttle(User $user): void
    {
        if (RateLimiter::tooManyAttempts($this->throttleKey($user), (int) config('security.max_attempts'))) {
            throw ValidationException::withMessages(['code' => 'Too many wrong codes. Try again in '.ceil(RateLimiter::availableIn($this->throttleKey($user)) / 60).' minutes.']);
        }
    }

    private function throttleKey(User $user): string
    {
        return "two-factor-profile:{$user->id}";
    }
}
