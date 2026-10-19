<?php

namespace App\Http\Requests\Auth;

use App\Audit\Audit;
use App\Auth\CodexAuthClient;
use App\Auth\CodexUnavailable;
use App\Auth\CodexUserSync;
use App\Auth\UserIdConflict;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;
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
            'password' => ['required', 'string', 'max:255'],
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
        $username = $this->codexUsername();
        $limits = [
            $this->throttleKey($username) => [(int) config('security.login_attempts'), 'too many attempts'],
            'login-address|'.$this->ip() => [(int) config('security.login_ip_attempts'), 'too many attempts from this address'],
        ];

        foreach ($limits as $key => [$max, $reason]) {
            $this->ensureIsNotRateLimited($key, $max, $reason);
        }

        // Counted before Codex is asked, so requests sent at the same moment cannot all slip under the limit; an outage gives it back.
        array_map(RateLimiter::hit(...), array_keys($limits));
        $giveBack = fn () => array_map(RateLimiter::decrement(...), array_keys($limits));

        try {
            $result = $codex->authenticate($username, $this->string('password')->toString());
        } catch (CodexUnavailable $exception) {
            $giveBack();
            report($exception);
            $this->audit('auth.login_failed', 'sign-in service unavailable');

            throw ValidationException::withMessages([
                'username' => 'Layanan masuk tidak dapat dihubungi saat ini. Coba lagi sebentar lagi.',
            ]);
        }

        if ($result === null) {
            $this->audit('auth.login_failed', 'invalid credentials');

            throw ValidationException::withMessages(['username' => trans('auth.failed')]);
        }

        try {
            $user = $sync->sync($result['user'], $result['office']);
        } catch (UserIdConflict $exception) {
            $giveBack();
            report($exception);
            $this->audit('auth.login_failed', 'user id conflict');

            throw ValidationException::withMessages(['username' => 'Data Anda di Codex tidak cocok dengan data di SIPEBRI. Hubungi Super Admin.']);
        }

        // Only failures count: the person's count is cleared, the address's gets this attempt back.
        RateLimiter::clear($this->throttleKey($username));
        RateLimiter::decrement('login-address|'.$this->ip());

        if ($user->trashed()) {
            $this->audit('auth.login_failed', 'inactive account', $user);

            throw ValidationException::withMessages(['username' => 'Akun Anda tidak aktif. Hubungi atasan atau bagian SDM.']);
        }

        return $user;
    }

    /** Failed sign-ins are evidence too; the typed username is kept only when it looks like one, never the password. */
    private function audit(string $event, string $reason, ?User $user = null): void
    {
        $typed = $this->loggableUsername();

        Audit::record($event, 'auth', str_replace('auth.', '', $event), $user, context: ['username' => $typed, 'reason' => $reason], outcome: 'failure', label: $user?->auditLabel() ?? $typed);
    }

    /**
     * People sometimes type the password in the username field, and the trail must not keep it. A staff number, a username, or
     * an email is kept as typed; anything else is replaced by a marker. Short plain words still pass, which no rule can tell apart.
     */
    private function loggableUsername(): string
    {
        $typed = $this->string('username')->toString();
        $looksLikeUsername = preg_match('/^[A-Za-z0-9._-]{1,30}$/', $typed) === 1 || filter_var($typed, FILTER_VALIDATE_EMAIL) !== false;

        return $looksLikeUsername ? $typed : '[bukan username]';
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
    private function ensureIsNotRateLimited(string $key, int $max, string $reason): void
    {
        if (! RateLimiter::tooManyAttempts($key, $max)) {
            return;
        }

        event(new Lockout($this));
        $seconds = RateLimiter::availableIn($key);

        // One entry per lockout: the blocked requests that follow are not new evidence and must not grow the trail.
        if (Cache::add('auth.lockout-audited:'.$key, true, $seconds)) {
            $this->audit('auth.locked_out', $reason);
        }

        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', ['seconds' => $seconds, 'minutes' => ceil($seconds / 60)]),
        ]);
    }

    /** One key for a person, however they typed it (email or username, any case): the Codex username decides. */
    private function throttleKey(string $username): string
    {
        return Str::transliterate(Str::lower($username).'|'.$this->ip());
    }
}
