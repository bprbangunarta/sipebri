<?php

namespace App\Http\Controllers;

use App\Enums\LoanStatus;
use App\Models\Collateral;
use App\Models\LoanApplication;
use App\Services\ReverseGeocoder;
use App\Support\Coordinates;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Sets or clears the position of the survey location or of one collateral while the survey is being filled in.
 * On site the surveyor taps "Mark location" and the phone's GPS is stored (source gps). Photos usually reach the system
 * later, through chat apps, and are filled in at the office, so the position can also be pasted (coordinates or a map link),
 * placed on the map, or taken from a photo that still carries GPS data. The uploading device's position is never guessed.
 */
class SurveyLocationController extends Controller
{
    public function store(Request $request, LoanApplication $loanApplication, ReverseGeocoder $geocoder): RedirectResponse
    {
        if ($blocked = $this->blocked($request, $loanApplication)) {
            return $blocked;
        }

        $data = $request->validate([
            ...$this->targetRules(),
            'coordinates' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitude'],
            'source' => ['nullable', 'in:gps,map'],
            'photo_id' => ['nullable', 'integer'],
        ]);

        [$latitude, $longitude, $source] = $this->resolve($request, $loanApplication, $data);
        $by = $request->user()->name;
        // A hint for people: the approximate address of this position. No answer (switched off, slow, failing) is fine.
        $address = $geocoder->lookup($latitude, $longitude);

        if ($data['target'] === 'survey') {
            $loanApplication->update([
                'survey_latitude' => $latitude, 'survey_longitude' => $longitude, 'survey_source' => $source,
                'survey_located_at' => now(), 'survey_located_by' => $by, 'survey_address' => $address,
            ]);
        } else {
            $this->collateral($loanApplication, (int) $data['collateral_id'])
                ->update(['latitude' => $latitude, 'longitude' => $longitude, 'location_source' => $source, 'located_at' => now(), 'located_by' => $by, 'location_address' => $address]);
        }

        return back()->with('success', 'Location saved.');
    }

    public function destroy(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        if ($blocked = $this->blocked($request, $loanApplication)) {
            return $blocked;
        }

        $data = $request->validate($this->targetRules());

        if ($data['target'] === 'survey') {
            $loanApplication->update(['survey_latitude' => null, 'survey_longitude' => null, 'survey_source' => null, 'survey_located_at' => null, 'survey_located_by' => null, 'survey_address' => null]);
        } else {
            $this->collateral($loanApplication, (int) $data['collateral_id'])
                ->update(['latitude' => null, 'longitude' => null, 'location_source' => null, 'located_at' => null, 'located_by' => null, 'location_address' => null]);
        }

        return back()->with('success', 'Location removed.');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function targetRules(): array
    {
        return [
            'target' => ['required', 'in:survey,collateral'],
            'collateral_id' => ['required_if:target,collateral', 'nullable', 'integer'],
        ];
    }

    /** Only the assigned surveyor, and only until the survey result is saved. */
    private function blocked(Request $request, LoanApplication $loan): ?RedirectResponse
    {
        abort_unless($loan->surveyor_id === $request->user()->id, 403, 'This file is not assigned to you.');

        return $loan->status !== LoanStatus::Scheduling ? back()->with('error', 'The survey result is locked.') : null;
    }

    /** A collateral can only be located through a file it is attached to. */
    private function collateral(LoanApplication $loan, int $id): Collateral
    {
        return $loan->collaterals()->whereKey($id)->firstOrFail();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{0: float, 1: float, 2: string} latitude, longitude, source (gps, map, paste or photo)
     */
    private function resolve(Request $request, LoanApplication $loan, array $data): array
    {
        if (filled($data['photo_id'] ?? null)) {
            $photo = $loan->photos()->whereKey((int) $data['photo_id'])->firstOrFail();
            $point = $photo->latitude !== null && $photo->longitude !== null ? Coordinates::inside((float) $photo->latitude, (float) $photo->longitude) : null;

            return $point !== null ? [...$point, 'photo'] : throw ValidationException::withMessages(['coordinates' => 'This photo carries no GPS position.']);
        }

        if (isset($data['latitude'], $data['longitude'])) {
            $point = Coordinates::inside((float) $data['latitude'], (float) $data['longitude']);

            return $point !== null ? [...$point, $data['source'] ?? 'map'] : throw ValidationException::withMessages(['coordinates' => 'The position must be inside Indonesia.']);
        }

        $text = (string) ($data['coordinates'] ?? '');

        if (Coordinates::isShortLink($text)) {
            throw ValidationException::withMessages(['coordinates' => 'Short map links hide the position. Open the link, then copy the coordinates or the full address from the browser.']);
        }

        $point = Coordinates::parse($text);

        return $point !== null
            ? [...$point, 'paste']
            : throw ValidationException::withMessages(['coordinates' => 'Paste coordinates such as -6.4643, 107.8083 or a Google Maps link with them; the position must be inside Indonesia.']);
    }
}
