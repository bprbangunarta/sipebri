<?php

namespace App\Http\Controllers\Auth;

use App\Audit\Audit;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Security\TwoFactor\EmailCode;
use App\Security\TwoFactor\TwoFactor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Second step of signing in. The person has already passed Codex; they are only signed in here,
 * after proving the code from their authenticator app, their email, or a recovery code.
 */
class TwoFactorChallengeController extends Controller
{
    public function __construct(private TwoFactor $twoFactor, private EmailCode $email) {}

    public function create(Request $request): Response|RedirectResponse
    {
        $user = $this->pending($request);

        if ($user === null) {
            return $this->restart($request);
        }

        $method = $this->twoFactor->methodFor($user);

        return Inertia::render('auth/two-factor-challenge', [
            'method' => $method,
            'email' => $method === TwoFactor::EMAIL ? $this->mask($user->email) : null,
            'resendIn' => $method === TwoFactor::EMAIL ? $this->email->secondsUntilResend($user) : 0,
            'name' => $user->name,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $this->pending($request);

        if ($user === null) {
            return $this->restart($request);
        }

        $data = $request->validate(['code' => ['required', 'string', 'max:32']]);
        $key = "two-factor-login:{$user->id}";

        if (RateLimiter::tooManyAttempts($key, (int) config('security.max_attempts'))) {
            $request->session()->forget('two_factor.login');

            return to_route('login')->withErrors(['username' => 'Terlalu banyak kode salah. Silakan masuk lagi.']);
        }

        if (! $this->twoFactor->verify($user, $data['code'])) {
            RateLimiter::hit($key, 15 * 60);
            Audit::record('auth.mfa_failed', 'auth', 'mfa_failed', $user, context: ['method' => $this->twoFactor->methodFor($user)], outcome: 'failure', actor: $user);

            throw ValidationException::withMessages(['code' => 'Kode tidak valid. Periksa lalu coba lagi.']);
        }

        RateLimiter::clear($key);
        Audit::record('auth.mfa_verified', 'auth', 'mfa_verified', $user, context: ['method' => $this->twoFactor->methodFor($user)], actor: $user);
        $remember = (bool) $request->session()->pull('two_factor.login.remember', false);
        $request->session()->forget('two_factor.login');

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function resend(Request $request): RedirectResponse
    {
        $user = $this->pending($request);

        if ($user === null || $this->twoFactor->methodFor($user) !== TwoFactor::EMAIL) {
            return $this->restart($request);
        }

        try {
            $sent = $this->email->issue($user);
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', 'Kode gagal dikirim. Coba lagi sebentar lagi.');
        }

        return $sent
            ? back()->with('success', 'Kode baru sudah dikirim.')
            : back()->with('error', "Tunggu {$this->email->secondsUntilResend($user)} detik sebelum meminta kode lagi.");
    }

    public function cancel(Request $request): RedirectResponse
    {
        $request->session()->forget('two_factor.login');

        return to_route('login');
    }

    /**
     * The person waiting for the second step, if the attempt is still valid and they are still active.
     */
    private function pending(Request $request): ?User
    {
        $login = $request->session()->get('two_factor.login');

        if (! is_array($login) || ($login['expires_at'] ?? 0) < now()->timestamp) {
            return null;
        }

        $user = User::query()->whereKey((int) $login['id'])->first();

        return $user !== null && $this->twoFactor->required($user) ? $user : null;
    }

    private function restart(Request $request): RedirectResponse
    {
        $request->session()->forget('two_factor.login');

        return to_route('login')->withErrors(['username' => 'Sesi masuk Anda berakhir. Silakan masuk lagi.']);
    }

    private function mask(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');

        return Str::substr($local, 0, 2).str_repeat('*', max(3, Str::length($local) - 2)).'@'.$domain;
    }
}
