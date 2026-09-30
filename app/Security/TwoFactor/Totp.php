<?php

namespace App\Security\TwoFactor;

/**
 * Time-based one-time passwords (RFC 6238, SHA-1, 30 s, 6 digits): what Google Authenticator, Microsoft
 * Authenticator, Authy and friends implement. Small enough to own instead of adding a dependency.
 */
class Totp
{
    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    public const PERIOD = 30;

    public static function generateSecret(int $bytes = 20): string
    {
        return self::base32Encode(random_bytes(max(1, $bytes)));
    }

    public static function code(string $secret, ?int $timestamp = null): string
    {
        return self::hotp($secret, intdiv($timestamp ?? time(), self::PERIOD));
    }

    /**
     * Whether the code is valid now, allowing one step either side for clock drift.
     * Returns the matched time step (so callers can refuse a replay) or null.
     */
    public static function verify(string $secret, string $code, int $window = 1, ?int $timestamp = null): ?int
    {
        $code = preg_replace('/\s+/', '', $code) ?? '';

        if (! preg_match('/^\d{6}$/', $code)) {
            return null;
        }

        $step = intdiv($timestamp ?? time(), self::PERIOD);

        for ($offset = -$window; $offset <= $window; $offset++) {
            if (hash_equals(self::hotp($secret, $step + $offset), $code)) {
                return $step + $offset;
            }
        }

        return null;
    }

    public static function uri(string $secret, string $account, string $issuer): string
    {
        return sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s&algorithm=SHA1&digits=6&period=%d',
            rawurlencode($issuer), rawurlencode($account), $secret, rawurlencode($issuer), self::PERIOD,
        );
    }

    private static function hotp(string $secret, int $counter): string
    {
        $hash = hash_hmac('sha1', pack('J', $counter), self::base32Decode($secret), true);
        $offset = ord($hash[19]) & 0x0F;
        $binary = ((ord($hash[$offset]) & 0x7F) << 24) | (ord($hash[$offset + 1]) << 16) | (ord($hash[$offset + 2]) << 8) | ord($hash[$offset + 3]);

        return str_pad((string) ($binary % 1_000_000), 6, '0', STR_PAD_LEFT);
    }

    private static function base32Encode(string $data): string
    {
        $bits = '';
        foreach (str_split($data) as $char) {
            $bits .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }

        $out = '';
        foreach (str_split($bits, 5) as $chunk) {
            $out .= self::ALPHABET[(int) bindec(str_pad($chunk, 5, '0'))];
        }

        return $out;
    }

    private static function base32Decode(string $secret): string
    {
        $bits = '';
        foreach (str_split(strtoupper(rtrim($secret, '='))) as $char) {
            $position = strpos(self::ALPHABET, $char);

            if ($position !== false) {
                $bits .= str_pad(decbin($position), 5, '0', STR_PAD_LEFT);
            }
        }

        $out = '';
        foreach (str_split($bits, 8) as $byte) {
            if (strlen($byte) === 8) {
                $out .= chr((int) bindec($byte));
            }
        }

        return $out;
    }
}
