<?php

use App\Services\ReverseGeocoder;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    config(['services.geocoder.enabled' => true, 'services.geocoder.endpoint' => 'https://geo.test/reverse', 'services.geocoder.user_agent' => 'SIPEBRI (test@example.com)']);
    Cache::flush();
    RateLimiter::clear('reverse-geocoder');
    Http::swap(new Factory);
});

it('turns a position into an approximate address and identifies itself', function () {
    Http::fake(['geo.test/*' => Http::response(['display_name' => 'Jalan Raya, Desa Sukamandi, Kecamatan Ciasem, Kabupaten Subang, Jawa Barat, 41256, Indonesia'])]);

    expect(app(ReverseGeocoder::class)->lookup(-6.4642957, 107.8083472))->toBe('Jalan Raya, Desa Sukamandi, Kecamatan Ciasem, Kabupaten Subang, Jawa Barat, 41256');

    Http::assertSent(fn (Request $r) => $r->hasHeader('User-Agent', 'SIPEBRI (test@example.com)') && $r['lat'] === -6.4642957 && $r['lon'] === 107.8083472 && $r['accept-language'] === 'id');
});

it('asks once per position and remembers the answer', function () {
    Http::fake(['geo.test/*' => Http::response(['display_name' => 'Desa X, Indonesia'])]);

    $geocoder = app(ReverseGeocoder::class);
    expect($geocoder->lookup(-6.46429, 107.80834))->toBe('Desa X')->and($geocoder->lookup(-6.464290001, 107.808340001))->toBe('Desa X');

    Http::assertSentCount(1);
});

it('gives no address, and never fails, when the service is off, broken, slow or busy', function (Closure $arrange) {
    $arrange();

    expect(app(ReverseGeocoder::class)->lookup(-6.4, 107.8))->toBeNull();
})->with([
    'switched off' => [fn () => config(['services.geocoder.enabled' => false])],
    'no endpoint' => [fn () => config(['services.geocoder.endpoint' => null])],
    'server error' => [fn () => Http::fake(['geo.test/*' => Http::response('nope', 500)])],
    'no name in the answer' => [fn () => Http::fake(['geo.test/*' => Http::response(['error' => 'Unable to geocode'])])],
    'connection failure' => [fn () => Http::fake(['geo.test/*' => fn () => throw new ConnectionException('timeout')])],
    'more than one request per second' => [function () {
        Http::fake(['geo.test/*' => Http::response(['display_name' => 'A'])]);
        RateLimiter::hit('reverse-geocoder', 1);
    }],
]);

it('keeps the address with the saved position, refreshes it when the position changes and clears it with the position', function () {
    [$analyst, $loan, $collateral] = surveyFile();
    Http::fake(['geo.test/*' => Http::sequence()->push(['display_name' => 'Desa A, Indonesia'])->push(['display_name' => 'Desa B, Indonesia'])->push(['error' => 'none'])]);
    $this->actingAs($analyst);

    $this->post(route('surveys.locations.store', $loan), ['target' => 'survey', 'coordinates' => '-6.4, 107.8'])->assertSessionHasNoErrors();
    expect($loan->fresh()->survey_address)->toBe('Desa A');

    RateLimiter::clear('reverse-geocoder');
    $this->post(route('surveys.locations.store', $loan), ['target' => 'survey', 'coordinates' => '-6.9, 107.1']);
    expect($loan->fresh()->survey_address)->toBe('Desa B');

    // A position whose address cannot be found is still saved, and the old address is not left behind.
    RateLimiter::clear('reverse-geocoder');
    $this->post(route('surveys.locations.store', $loan), ['target' => 'collateral', 'collateral_id' => $collateral->id, 'coordinates' => '-6.1, 106.9'])->assertSessionHasNoErrors();
    expect($collateral->fresh()->latitude)->not->toBeNull()->and($collateral->fresh()->location_address)->toBeNull();

    $this->delete(route('surveys.locations.destroy', $loan), ['target' => 'survey']);
    expect($loan->fresh()->survey_address)->toBeNull();
});

it('saves the position even when the address service is down, and copies the address to the survey record', function () {
    Storage::fake('public');
    [$analyst, $loan] = surveyFile();
    Http::fake(['geo.test/*' => Http::sequence()->push('down', 503)->push(['display_name' => 'Desa C, Indonesia'])]);
    $this->actingAs($analyst);

    $this->post(route('surveys.locations.store', $loan), ['target' => 'survey', 'coordinates' => '-6.4, 107.8'])->assertSessionHasNoErrors()->assertSessionMissing('errors');
    expect($loan->fresh()->survey_latitude)->not->toBeNull()->and($loan->fresh()->survey_address)->toBeNull();

    RateLimiter::clear('reverse-geocoder');
    $this->post(route('surveys.locations.store', $loan), ['target' => 'survey', 'coordinates' => '-6.5, 107.9']);
    $this->post(route('surveys.photos.store', $loan), ['photo' => UploadedFile::fake()->image('s.jpg')]);
    $this->post(route('surveys.store', $loan), ['note' => 'ok'])->assertSessionHas('success');

    expect($loan->surveys()->firstOrFail()->location_address)->toBe('Desa C')->and($loan->fresh()->survey_address)->toBeNull();
});

it('shows the address on the survey page and the file page', function () {
    [$analyst, $loan, $collateral] = surveyFile();
    $collateral->update(['latitude' => -6.4, 'longitude' => 107.8, 'location_source' => 'paste', 'location_address' => 'Desa D, Kabupaten Subang']);

    $this->actingAs($analyst)->get(route('surveys.show', $loan))->assertInertia(fn (Illuminate\Testing\Fluent\AssertableJson|AssertableInertia $page) => $page
        ->where('locations.1.location.address', 'Desa D, Kabupaten Subang'));
});

it('keeps the test suite from calling the real geocoder', function () {
    expect(file_get_contents(base_path('phpunit.xml')))->toContain('<env name="REVERSE_GEOCODING" value="false"/>');
});

it('fills in the addresses that are missing, and leaves the ones that exist', function () {
    [$analyst, $loan, $collateral] = surveyFile();
    $collateral->update(['latitude' => -6.4, 'longitude' => 107.8, 'location_source' => 'paste']);
    $loan->update(['survey_latitude' => -6.5, 'survey_longitude' => 107.9, 'survey_source' => 'gps', 'survey_address' => 'Kept']);
    Http::fake(['geo.test/*' => Http::response(['display_name' => 'Desa E, Indonesia'])]);

    $this->artisan('locations:geocode', ['--dry-run' => true])->assertSuccessful();
    expect($collateral->fresh()->location_address)->toBeNull();

    $this->artisan('locations:geocode')->assertSuccessful();
    expect($collateral->fresh()->location_address)->toBe('Desa E')->and($loan->fresh()->survey_address)->toBe('Kept');
});
