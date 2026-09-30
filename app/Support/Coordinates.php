<?php

namespace App\Support;

/**
 * Reads a geographic coordinate from what people actually paste: "-6.4643, 107.8083", a Google Maps link
 * (?q=, ll=, @lat,lng, !3d..!4d.., or a WhatsApp location message) or a geo: URI. Short links (maps.app.goo.gl) carry no
 * coordinates and are not followed. Coordinates must fall inside Indonesia, which also catches swapped or mistyped values.
 */
class Coordinates
{
    public const LAT_RANGE = [-11.5, 6.5];

    public const LNG_RANGE = [94.5, 141.5];

    /**
     * @return array{0: float, 1: float}|null [latitude, longitude]
     */
    public static function parse(string $input): ?array
    {
        $text = trim(rawurldecode(html_entity_decode($input)));
        $number = '(-?\d{1,3}(?:\.\d+)?)';

        foreach ([
            '/^geo:'.$number.',\s*'.$number.'/i',
            '/!3d'.$number.'!4d'.$number.'/',
            '/@'.$number.',\s*'.$number.'/',
            '/[?&](?:q|ll|query|destination|center|daddr)='.$number.',\s*\+?'.$number.'/i',
            '/^'.$number.'\s*[,;]\s*'.$number.'$/',
            '/^'.$number.'\s+'.$number.'$/',
        ] as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                return self::inside((float) $m[1], (float) $m[2]);
            }
        }

        return null;
    }

    /** True when the text is a link that hides its coordinates behind a redirect. */
    public static function isShortLink(string $input): bool
    {
        return (bool) preg_match('~(?:maps\.app\.goo\.gl|goo\.gl/maps)~i', $input);
    }

    /**
     * @return array{0: float, 1: float}|null
     */
    public static function inside(float $latitude, float $longitude): ?array
    {
        return $latitude >= self::LAT_RANGE[0] && $latitude <= self::LAT_RANGE[1] && $longitude >= self::LNG_RANGE[0] && $longitude <= self::LNG_RANGE[1]
            ? [round($latitude, 7), round($longitude, 7)]
            : null;
    }

    public static function mapsUrl(float|string $latitude, float|string $longitude): string
    {
        return 'https://www.google.com/maps?q='.$latitude.','.$longitude;
    }
}
