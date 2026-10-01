<?php

namespace App\Http\Requests\Auth;

use App\Audit\Audit;
use App\Auth\CodexAuthClient;
use App\Auth\CodexUnavailable;
use App\Auth\CodexUserSync;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge(['username' => trim($this->string('username')->toString())]);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Checks the credentials with Codex and mirrors the person into the local users. Everyone Codex knows is
     * kept locally, but only active ones get through. Returns the verified user; the controller then signs
     * them in, or first asks for the second factor.
     *
     * @throws ValidationException
     */
    public function authenticate(CodexAuthClient $codex, CodexUserSync $sync): User
    {
        $this->ensureIsNotRateLimited();

        try {
            $result = $codex->authenticate($this->codexUsername(), $this->string('password')->toString());
        } catch (CodexUnavailable $exception) {
            report($exception);
            $this->audit('auth.login_failed', 'sign-in service unavailable');

            throw ValidationException::withMessages([
                'username' => 'Layanan masuk tidak dapat dihubungi saat ini. Coba lagi sebentar lagi.',
            ]);
        }

        if ($result === null) {
            RateLimiter::hit($this->throttleKey());
            $this->audit('auth.login_failed', 'invalid credentials');

            throw ValidationException::withMessages(['username' => trans('auth.failed')]);
        }

        $user = $sync->sync($result['user'], $result['office']);

        if ($user->trashed()) {
            $this->audit('auth.login_failed', 'inactive account', $user);

            throw ValidationException::withMessages(['username' => 'Akun Anda tidak aktif. Hubungi atasan atau bagian SDM.']);
        }

        RateLimiter::clear($this->throttleKey());

        return $user;
    }

    /** Failed sign-ins are evidence too; only the typed username is kept, never the password. */
    private function audit(string $event, string $reason, ?User $user = null): void
    {
        Audit::record($event, 'auth', str_replace('auth.', '', $event), $user, context: ['username' => $this->string('username')->toString(), 'reason' => $reason], outcome: 'failure', label: $user?->auditLabel() ?? $this->string('username')->toString());
    }

    /**
     * People may type their email or their username. Codex is asked by username, so an email that
     * belongs to someone already stored here is translated to that username; anything else is
     * passed on as typed.
     */
    private function codexUsername(): string
    {
        $login = $this->string('username')->toString();

        if (! str_contains($login, '@')) {
            return $login;
        }

        return User::withTrashed()->where('email', Str::lower($login))->value('username') ?? $login;
    }

    /**
     * @throws ValidationException
     */
    private function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));
        $this->audit('auth.locked_out', 'too many attempts');

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', ['seconds' => $seconds, 'minutes' => ceil($seconds / 60)]),
        ]);
    }

    private function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('username')).'|'.$this->ip());
    }
}
