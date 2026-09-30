<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;

/**
 * Turns a position into an approximate address with OpenStreetMap's Nominatim. A hint for people, not a fact: in villages
 * the street and house number are often missing. It never blocks anything: a disabled, slow, failing or rate-limited
 * service just yields no address. Results are cached by position (Nominatim's usage policy asks for it) and requests are
 * kept to one per second with an identifying User-Agent, as the public service requires.
 *
 * The position of a customer's home or collateral is sent to that service, so the whole feature can be switched off
 * with REVERSE_GEOCODING=false, or pointed at the bank's own Nominatim with GEOCODER_ENDPOINT.
 */
class ReverseGeocoder
{
    private const THROTTLE_KEY = 'reverse-geocoder';

    public function lookup(float $latitude, float $longitude): ?string
    {
        if (! config('services.geocoder.enabled') || blank(config('services.geocoder.endpoint'))) {
            return null;
        }

        $key = sprintf('geocode:%.5f,%.5f', $latitude, $longitude);

        if (is_string($cached = Cache::get($key))) {
            return $cached;
        }

        if (RateLimiter::tooManyAttempts(self::THROTTLE_KEY, 1)) {
            return null;
        }

        RateLimiter::hit(self::THROTTLE_KEY, 1);

        try {
            $response = Http::withUserAgent((string) config('services.geocoder.user_agent'))
                ->acceptJson()
                ->connectTimeout(2)
                ->timeout((int) config('services.geocoder.timeout'))
                ->get((string) config('services.geocoder.endpoint'), [
                    'format' => 'jsonv2', 'lat' => $latitude, 'lon' => $longitude, 'zoom' => 18, 'addressdetails' => 0, 'accept-language' => 'id',
                ]);
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }

        $address = $response->successful() ? $this->tidy($response->json('display_name')) : null;

        if ($address !== null) {
            Cache::put($key, $address, now()->addDays(30));
        }

        return $address;
    }

    private function tidy(mixed $name): ?string
    {
        if (! is_string($name) || trim($name) === '') {
            return null;
        }

        return mb_substr(trim(preg_replace('/,\s*Indonesia$/u', '', $name) ?? $name), 0, 500);
    }
}
