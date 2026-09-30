<?php

namespace App\Security\TwoFactor;

use App\Mail\LoginCode;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

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

        return $sentAt === null ? 0 : (int) max(0, $sentAt + (int) config('security.email_resend_seconds') - now()->getTimestamp());
    }

    /**
     * Send a fresh code. Returns false (and sends nothing) while the resend cooldown is running.
     */
    public function issue(User $user): bool
    {
        if ($this->secondsUntilResend($user) > 0) {
            return false;
        }

        $length = (int) config('security.code_length');
        $code = str_pad((string) random_int(0, (10 ** $length) - 1), $length, '0', STR_PAD_LEFT);
        $ttl = (int) config('security.email_code_ttl');

        Cache::put($this->key($user), ['hash' => $this->hash($user, $code), 'sent_at' => now()->getTimestamp(), 'attempts' => 0], now()->addMinutes($ttl));

        Mail::to($user->email)->send(new LoginCode($code, $ttl));

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

    private function hash(User $user, string $code): string
    {
        return hash_hmac('sha256', "{$user->id}|{$code}", (string) config('app.key'));
    }
}
