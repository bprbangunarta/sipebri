<?php

namespace App\Security\TwoFactor;

use App\Models\User;
use Illuminate\Support\Str;

/**
 * Single-use codes that let someone with an authenticator app sign in when the device is lost.
 * They are shown once; only hashes are stored.
 */
class RecoveryCodes
{
    /**
     * Replace the stored codes and return the plain ones (to show once).
     *
     * @return list<string>
     */
    public function regenerate(User $user): array
    {
        $plain = [];

        for ($i = 0; $i < (int) config('security.recovery_codes'); $i++) {
            $plain[] = Str::lower(Str::random(5)).'-'.Str::lower(Str::random(5));
        }

        $user->mfa_recovery_codes = array_map($this->hash(...), $plain);
        $user->save();

        return $plain;
    }

    /**
     * Use up a recovery code. Returns whether it was valid.
     */
    public function consume(User $user, string $code): bool
    {
        $hash = $this->hash(Str::lower(trim($code)));
        $codes = $user->mfa_recovery_codes ?? [];

        if (! in_array($hash, $codes, true)) {
            return false;
        }

        $user->mfa_recovery_codes = array_values(array_diff($codes, [$hash]));
        $user->save();

        return true;
    }

    public function remaining(User $user): int
    {
        return count($user->mfa_recovery_codes ?? []);
    }

    private function hash(string $code): string
    {
        return hash_hmac('sha256', $code, (string) config('app.key'));
    }
}
