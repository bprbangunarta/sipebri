<?php

namespace App\Console\Commands;

use App\Models\Collateral;
use App\Models\LoanApplication;
use App\Models\LoanSurvey;
use App\Services\ReverseGeocoder;
use Illuminate\Console\Command;

class GeocodeLocations extends Command
{
    protected $signature = 'locations:geocode {--dry-run : Only count the positions without an address}';

    protected $description = 'Find the approximate address of saved positions that have none (e.g. the service was down when they were marked)';

    public function handle(ReverseGeocoder $geocoder): int
    {
        if (! config('services.geocoder.enabled')) {
            $this->warn('Reverse geocoding is switched off (REVERSE_GEOCODING=false).');

            return self::SUCCESS;
        }

        $targets = [
            'collaterals' => [Collateral::query()->whereNotNull('latitude')->whereNull('location_address'), 'latitude', 'longitude', 'location_address'],
            'survey locations (files)' => [LoanApplication::query()->whereNotNull('survey_latitude')->whereNull('survey_address'), 'survey_latitude', 'survey_longitude', 'survey_address'],
            'saved surveys' => [LoanSurvey::query()->whereNotNull('latitude')->whereNull('location_address'), 'latitude', 'longitude', 'location_address'],
        ];
        $found = 0;
        $missing = 0;

        foreach ($targets as $label => [$query, $lat, $lng, $column]) {
            $rows = $query->get();
            $this->line("{$label}: {$rows->count()} without an address");

            if ($this->option('dry-run')) {
                continue;
            }

            foreach ($rows as $row) {
                $address = $geocoder->lookup((float) $row->getAttribute($lat), (float) $row->getAttribute($lng));
                $address === null ? $missing++ : $found++;
                $address !== null && $row->update([$column => $address]);
                (app()->runningUnitTests() ? null : sleep(1)); // the public service allows one request per second
            }
        }

        $this->info("Addresses found: {$found}; not found: {$missing}.");

        return self::SUCCESS;
    }
}
