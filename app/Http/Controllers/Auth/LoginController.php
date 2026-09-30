<?php

namespace App\Http\Controllers\Auth;

use App\Audit\Audit;
use App\Auth\CodexAuthClient;
use App\Auth\CodexUserSync;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Security\TwoFactor\TwoFactor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class LoginController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('auth/login');
    }

    public function store(LoginRequest $request, CodexAuthClient $codex, CodexUserSync $sync, TwoFactor $twoFactor): RedirectResponse
    {
        $user = $request->authenticate($codex, $sync);

        // Only people who turned two-factor on are challenged. The person is not signed in until the code checks out.
        if ($twoFactor->required($user)) {
            try {
                $twoFactor->begin($user);
            } catch (Throwable $exception) {
                report($exception);

                throw ValidationException::withMessages(['username' => 'We could not send the verification code. Please try again shortly.']);
            }

            Audit::record('auth.password_verified', 'auth', 'password_verified', $user, context: ['next' => 'two-factor challenge'], actor: $user);
            $request->session()->put('two_factor.login', [
                'id' => $user->id,
                'remember' => $request->boolean('remember'),
                'expires_at' => now()->addMinutes((int) config('security.challenge_ttl'))->timestamp,
            ]);

            return to_route('two-factor.challenge');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
