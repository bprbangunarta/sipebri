<?php

namespace App\Security\TwoFactor;

use App\Audit\Audit;
use App\Mail\LoginCode;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;

/**
 * One-time codes sent to the person's email. Only a keyed hash of the code is kept (in the cache),
 * it expires, it can be tried a limited number of times, and a new one can only be requested after a cooldown.
 */
class EmailCode
{
    /** Whether the person has an email address that can receive the code (Codex placeholders cannot). */
    public function canReceive(User $user): bool
    {
        return filled($user->email) && ! str_ends_with($user->email, '@codex.invalid');
    }

    public function secondsUntilResend(User $user): int
    {
        $sentAt = Cache::get($this->key($user))['sent_at'] ?? null;

        $cooldown = $sentAt === null ? 0 : (int) max(0, $sentAt + (int) config('security.email_resend_seconds') - now()->getTimestamp());
        $capped = RateLimiter::tooManyAttempts($this->capKey($user), (int) config('security.email_max_per_hour')) ? RateLimiter::availableIn($this->capKey($user)) : 0;

        return max($cooldown, $capped);
    }

    /**
     * Send a fresh code. Returns false (and sends nothing) while the resend cooldown or the hourly cap is running.
     * Every send is written to the audit trail (never the code itself).
     */
    public function issue(User $user): bool
    {
        if ($this->secondsUntilResend($user) > 0) {
            if (RateLimiter::tooManyAttempts($this->capKey($user), (int) config('security.email_max_per_hour'))) {
                Audit::record('auth.mfa_code_throttled', 'auth', 'mfa_code_throttled', $user, context: ['method' => 'email'], outcome: 'denied', actor: $user);
            }

            return false;
        }

        $length = (int) config('security.code_length');
        $code = str_pad((string) random_int(0, (10 ** $length) - 1), $length, '0', STR_PAD_LEFT);
        $ttl = (int) config('security.email_code_ttl');

        Cache::put($this->key($user), ['hash' => $this->hash($user, $code), 'sent_at' => now()->getTimestamp(), 'attempts' => 0], now()->addMinutes($ttl));

        try {
            Mail::to($user->email)->send(new LoginCode($code, $ttl, $user->name));
        } catch (Throwable $exception) {
            Cache::forget($this->key($user));
            Audit::record('auth.mfa_code_send_failed', 'auth', 'mfa_code_send_failed', $user, context: ['method' => 'email', 'error' => class_basename($exception)], outcome: 'failure', actor: $user);

            throw $exception;
        }

        RateLimiter::hit($this->capKey($user), 3600);
        Audit::record('auth.mfa_code_sent', 'auth', 'mfa_code_sent', $user, context: ['method' => 'email'], actor: $user);

        return true;
    }

    public function verify(User $user, string $code): bool
    {
        $entry = Cache::get($this->key($user));

        if ($entry === null) {
            return false;
        }

        if ($entry['attempts'] >= (int) config('security.max_attempts')) {
            $this->forget($user);

            return false;
        }

        if (hash_equals($entry['hash'], $this->hash($user, preg_replace('/\s+/', '', $code) ?? ''))) {
            $this->forget($user);

            return true;
        }

        $entry['attempts']++;
        Cache::put($this->key($user), $entry, now()->addMinutes((int) config('security.email_code_ttl')));

        return false;
    }

    public function forget(User $user): void
    {
        Cache::forget($this->key($user));
    }

    private function key(User $user): string
    {
        return "two-factor:email:{$user->id}";
    }

    private function capKey(User $user): string
    {
        return "two-factor:email-cap:{$user->id}";
    }

    private function hash(User $user, string $code): string
    {
        return hash_hmac('sha256', "{$user->id}|{$code}", (string) config('app.key'));
    }
}
