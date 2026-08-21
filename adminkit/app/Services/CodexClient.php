<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Klien API Codex (sistem data nasabah). OAuth2 client_credentials:
 * token disimpan di cache sampai mendekati kedaluwarsa, dan diperbarui SEKALI
 * bila server menolak dengan 401.
 */
class CodexClient
{
    private const TOKEN_KEY = 'codex:access-token';

    /** Data nasabah mentah dari Codex; null bila NIK tidak terdaftar (404). */
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
            throw new RuntimeException("Codex menolak permintaan (HTTP {$response->status()}).");
        }

        $data = $response->json('data');

        return is_array($data) ? $data : null;
    }

    private function get(string $nik): Response
    {
        return $this->http()
            ->withToken($this->token())
            ->get('/api/customers/'.rawurlencode($nik));
    }

    private function token(): string
    {
        $cached = Cache::get(self::TOKEN_KEY);

        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        $response = $this->http()->asJson()->post('/oauth/token', [
            'client_id' => config('services.codex.client_id'),
            'client_secret' => config('services.codex.client_secret'),
            'grant_type' => 'client_credentials',
        ]);

        if ($response->failed()) {
            throw new RuntimeException("Gagal mengambil token Codex (HTTP {$response->status()}).");
        }

        $token = $response->json('access_token');
        $expires = (int) $response->json('expires_in');

        if (! is_string($token) || $token === '' || $expires <= 0) {
            throw new RuntimeException('Respons token Codex tidak sesuai.');
        }

        $ttl = max(60, $expires - (int) config('services.codex.token_skew'));
        Cache::put(self::TOKEN_KEY, $token, now()->addSeconds($ttl));

        return $token;
    }

    private function http(): PendingRequest
    {
        return Http::baseUrl(rtrim((string) config('services.codex.base_url'), '/'))
            ->acceptJson()
            ->timeout((float) config('services.codex.timeout'))
            ->connectTimeout((float) config('services.codex.connect_timeout'))
            ->retry(2, 200, fn ($e) => $e instanceof ConnectionException, throw: false);
    }
}
