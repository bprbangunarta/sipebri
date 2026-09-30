<?php

namespace App\Support;

use App\Models\Collateral;
use App\Models\CollateralType;
use App\Models\LoanApplication;

/**
 * The places of a file that can have a position: the survey location (where the surveyor did the survey) and each attached
 * collateral, with whatever position was recorded, in the shape the survey page and the file page render (list plus map).
 * While a survey is being filled in the survey location is the one on the file; once saved it is the one of the latest survey.
 */
class LocationTargets
{
    /**
     * @return list<array{key: string, type: string, collateral_id: int|null, label: string, detail: string, location: array<string, mixed>|null}>
     */
    public static function for(LoanApplication $loan, ?string $address = null): array
    {
        $types = CollateralType::query()->pluck('name', 'code');

        $targets = [[
            'key' => 'survey',
            'type' => 'survey',
            'collateral_id' => null,
            'label' => 'Survey location',
            'detail' => (string) $address,
            'location' => self::surveyLocation($loan),
        ]];

        foreach ($loan->collaterals as $c) {
            /** @var Collateral $c */
            $targets[] = [
                'key' => 'collateral:'.$c->id,
                'type' => 'collateral',
                'collateral_id' => $c->id,
                'label' => trim(($c->cbs_id ?? "#{$c->id}").' — '.$c->owner_name),
                'detail' => collect([
                    $c->document_number ? 'Doc '.$c->document_number : null,
                    $types[$c->collateral_type_code] ?? $c->collateral_type_code,
                    $c->owner_address ?: $c->description,
                ])->filter()->implode(' · '),
                'location' => self::location($c->latitude, $c->longitude, $c->location_source, $c->located_at?->format('d M Y H:i'), $c->located_by),
            ];
        }

        return $targets;
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function surveyLocation(LoanApplication $loan): ?array
    {
        if ($loan->survey_latitude !== null) {
            return self::location($loan->survey_latitude, $loan->survey_longitude, $loan->survey_source, $loan->survey_located_at?->format('d M Y H:i'), $loan->survey_located_by);
        }

        $survey = $loan->surveys()->reorder('id', 'desc')->first();

        return $survey !== null ? self::location($survey->latitude, $survey->longitude, $survey->location_source, $survey->created_at?->format('d M Y H:i'), $survey->created_by) : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function location(?string $latitude, ?string $longitude, ?string $source, ?string $at, ?string $by): ?array
    {
        if ($latitude === null || $longitude === null) {
            return null;
        }

        return [
            'latitude' => (float) $latitude, 'longitude' => (float) $longitude, 'source' => $source, 'located_at' => $at, 'located_by' => $by,
            'maps_url' => Coordinates::mapsUrl($latitude, $longitude),
        ];
    }
}
