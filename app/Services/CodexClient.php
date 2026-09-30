<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Client for the Codex customer API (OAuth2 client_credentials). The token is cached until it is
 * about to expire and refreshed ONCE when the server answers 401.
 */
class CodexClient
{
    private const TOKEN_KEY = 'codex:access-token';

    /**
     * Raw customer data, or null when the national ID is not registered (404).
     *
     * @return array<string, mixed>|null
     */
    public function customer(string $nik): ?array
    {
        $response = $this->get($nik);

        if ($response->status() === 401) {
            Cache::forget(self::TOKEN_KEY);
            $response = $this->get($nik);
        }

        if ($response->status() === 404) {
            return null;
        }

        if ($response->failed()) {
            throw new RuntimeException("Codex rejected the request (HTTP {$response->status()}).");
        }

        $data = $response->json('data');

        return is_array($data) ? $data : null;
    }

    private function get(string $nik): Response
    {
        return $this->http()->withToken($this->token())->get('/api/customers/'.rawurlencode($nik));
    }

    private function token(): string
    {
        $cached = Cache::get(self::TOKEN_KEY);

        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        $response = $this->http()->asJson()->post('/oauth/token', [
            'client_id' => config('services.codex.id'),
            'client_secret' => config('services.codex.secret'),
            'grant_type' => 'client_credentials',
        ]);

        if ($response->failed()) {
            throw new RuntimeException("Could not obtain a Codex token (HTTP {$response->status()}).");
        }

        $token = $response->json('access_token');
        $expires = (int) $response->json('expires_in');

        if (! is_string($token) || $token === '' || $expires <= 0) {
            throw new RuntimeException('Unexpected Codex token response.');
        }

        Cache::put(self::TOKEN_KEY, $token, now()->addSeconds(max(60, $expires - (int) config('services.codex.token_skew'))));

        return $token;
    }

    private function http(): PendingRequest
    {
        return Http::baseUrl(rtrim((string) config('services.codex.endpoint'), '/'))
            ->withOptions(['verify' => filter_var(config('services.codex.verify'), FILTER_VALIDATE_BOOL)])
            ->acceptJson()
            ->timeout((float) config('services.codex.timeout'))
            ->connectTimeout((float) config('services.codex.connect_timeout'))
            ->retry(2, 200, fn ($e) => $e instanceof ConnectionException, throw: false);
    }
}
