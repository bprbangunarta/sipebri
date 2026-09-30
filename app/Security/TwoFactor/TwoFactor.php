<?php

namespace App\Security\TwoFactor;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

/**
 * Entry point for two-factor authentication: is it on, which method does a person use, and does a code
 * they typed prove it is them. Never used for inactive people (they are refused before the second step).
 */
class TwoFactor
{
    public const EMAIL = 'email';

    public const TOTP = 'totp';

    public function __construct(private EmailCode $email, private RecoveryCodes $recovery) {}

    /** The global switch (MFA_ENABLED). */
    public function enabled(): bool
    {
        return (bool) config('security.mfa_enabled');
    }

    /** The confirmed method of a person, or null when they have none or the feature is off. */
    public function methodFor(User $user): ?string
    {
        return $this->enabled() && $user->mfa_confirmed_at !== null ? $user->mfa_method : null;
    }

    /** Whether signing in must include the second step. */
    public function required(User $user): bool
    {
        return $this->methodFor($user) !== null;
    }

    /**
     * Prepare the second step: an email person gets a code (unless one was just sent).
     */
    public function begin(User $user): void
    {
        if ($this->methodFor($user) === self::EMAIL) {
            $this->email->issue($user);
        }
    }

    /**
     * Check a code against the person's confirmed method; recovery codes also work for authenticator users.
     */
    public function verify(User $user, string $code): bool
    {
        return match ($this->methodFor($user)) {
            self::EMAIL => $this->email->verify($user, $code),
            self::TOTP => $this->verifyTotp($user, $code) || $this->recovery->consume($user, $code),
            default => false,
        };
    }

    /**
     * Check a TOTP code, refusing a time step that was already used (a code cannot be replayed).
     */
    public function verifyTotp(User $user, string $code, ?string $secret = null): bool
    {
        $step = Totp::verify($secret ?? (string) $user->mfa_secret, $code);

        if ($step === null) {
            return false;
        }

        return Cache::add("two-factor:totp-used:{$user->id}:{$step}", true, now()->addSeconds(Totp::PERIOD * 3));
    }

    /**
     * @return array{method: string|null, enabled: bool}
     */
    public function summary(User $user): array
    {
        return ['enabled' => $this->enabled(), 'method' => $this->methodFor($user)];
    }
}
