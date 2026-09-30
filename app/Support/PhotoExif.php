<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Throwable;

/**
 * Best-effort read of the GPS position and time a phone wrote into a photo. Chat apps (WhatsApp) strip this, so a photo
 * without it is normal and never an error.
 */
class PhotoExif
{
    /**
     * @return array{latitude: float|null, longitude: float|null, taken_at: Carbon|null}
     */
    public static function read(string $path): array
    {
        $empty = ['latitude' => null, 'longitude' => null, 'taken_at' => null];

        if (! function_exists('exif_read_data')) {
            return $empty;
        }

        try {
            $exif = @exif_read_data($path);
        } catch (Throwable) {
            return $empty;
        }

        if (! is_array($exif)) {
            return $empty;
        }

        $latitude = isset($exif['GPSLatitude'], $exif['GPSLatitudeRef']) ? self::toDecimal((array) $exif['GPSLatitude'], (string) $exif['GPSLatitudeRef']) : null;
        $longitude = isset($exif['GPSLongitude'], $exif['GPSLongitudeRef']) ? self::toDecimal((array) $exif['GPSLongitude'], (string) $exif['GPSLongitudeRef']) : null;
        $inside = $latitude !== null && $longitude !== null ? Coordinates::inside($latitude, $longitude) : null;

        try {
            $taken = isset($exif['DateTimeOriginal']) ? Carbon::createFromFormat('Y:m:d H:i:s', (string) $exif['DateTimeOriginal']) : null;
        } catch (Throwable) {
            $taken = null;
        }

        return ['latitude' => $inside[0] ?? null, 'longitude' => $inside[1] ?? null, 'taken_at' => $taken ?: null];
    }

    /**
     * Degrees/minutes/seconds as written in EXIF ("6/1", "27/1", "3645/100") to decimal degrees.
     *
     * @param  array<int, string|int|float>  $parts
     */
    public static function toDecimal(array $parts, string $reference): ?float
    {
        if (count($parts) < 3) {
            return null;
        }

        $value = array_map(function (string|int|float $part): ?float {
            if (is_string($part) && str_contains($part, '/')) {
                [$a, $b] = array_pad(explode('/', $part, 2), 2, '1');

                return (float) $b === 0.0 ? null : (float) $a / (float) $b;
            }

            return (float) $part;
        }, array_slice($parts, 0, 3));

        if (in_array(null, $value, true)) {
            return null;
        }

        $decimal = $value[0] + $value[1] / 60 + $value[2] / 3600;

        return in_array(strtoupper($reference), ['S', 'W'], true) ? -$decimal : $decimal;
    }
}
