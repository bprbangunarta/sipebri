<?php

use App\Audit\AuditLog;
use App\Enums\LoanStatus;
use App\Models\Collateral;
use App\Models\LoanApplication;
use App\Models\LoanSchedule;
use App\Models\LoanSurveyPhoto;
use App\Models\User;
use App\Support\Coordinates;
use App\Support\PhotoExif;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

function surveyFile(): array
{
    test()->seed(RoleSeeder::class);
    $analyst = User::factory()->create()->assignRole('Staff Analis & Appraisal');
    $loan = LoanApplication::create([
        'application_code' => '00700001', 'application_date' => now(), 'status' => LoanStatus::Scheduling->value, 'nik' => '3201000000000001',
        'full_name' => 'Siti', 'surveyor_id' => $analyst->id, 'survey_date' => now()->toDateString(), 'created_by' => $analyst->id,
    ]);
    $collateral = Collateral::create(['collateral_type_code' => '05', 'owner_name' => 'ONIH', 'document_number' => 'SHM 1']);
    $loan->collaterals()->attach($collateral->id);

    return [$analyst, $loan, $collateral];
}

it('reads coordinates from what people paste', function (string $input, ?array $expected) {
    expect(Coordinates::parse($input))->toBe($expected);
})->with([
    'plain' => ['-6.4643579, 107.8083288', [-6.4643579, 107.8083288]],
    'spaces only' => ['-6.4643 107.8083', [-6.4643, 107.8083]],
    'semicolon' => ['-6.4643;107.8083', [-6.4643, 107.8083]],
    'whatsapp / maps ?q=' => ['https://maps.google.com/?q=-6.4643579,107.8083288&entry=gps', [-6.4643579, 107.8083288]],
    'encoded comma' => ['https://www.google.com/maps?q=-6.4643%2C107.8083', [-6.4643, 107.8083]],
    'at form' => ['https://www.google.com/maps/place/X/@-6.4643579,107.8083288,17z/data=!3m1', [-6.4643579, 107.8083288]],
    'pin form' => ['https://www.google.com/maps/place/X/data=!3d-6.46!4d107.80', [-6.46, 107.8]],
    'geo uri' => ['geo:-6.46,107.80', [-6.46, 107.8]],
    'outside indonesia' => ['48.8584, 2.2945', null],
    'swapped' => ['107.8083, -6.4643', null],
    'nonsense' => ['near the mosque', null],
]);

it('knows short map links', function () {
    expect(Coordinates::isShortLink('https://maps.app.goo.gl/AbC123'))->toBeTrue()
        ->and(Coordinates::isShortLink('https://maps.google.com/?q=-6,107'))->toBeFalse();
});

it('converts EXIF degrees to decimal, with the hemisphere', function () {
    expect(PhotoExif::toDecimal(['6/1', '27/1', '3645/100'], 'S'))->toEqualWithDelta(-6.460125, 0.000001)
        ->and(PhotoExif::toDecimal(['107/1', '48/1', '0/1'], 'E'))->toEqualWithDelta(107.8, 0.000001)
        ->and(PhotoExif::toDecimal(['6/1'], 'S'))->toBeNull()
        ->and(PhotoExif::toDecimal(['6/0', '0/1', '0/1'], 'S'))->toBeNull();
});

it('locates the survey location and a collateral, and overwrites or clears them', function () {
    [$analyst, $loan, $collateral] = surveyFile();
    $this->actingAs($analyst);

    $this->post(route('surveys.locations.store', $loan), ['target' => 'survey', 'coordinates' => 'https://maps.google.com/?q=-6.4643579,107.8083288'])->assertSessionHasNoErrors();
    $this->post(route('surveys.locations.store', $loan), ['target' => 'collateral', 'collateral_id' => $collateral->id, 'latitude' => -6.5, 'longitude' => 107.9])->assertSessionHasNoErrors();

    $loan->refresh();
    $collateral->refresh();
    expect((float) $loan->survey_latitude)->toBe(-6.4643579)->and($loan->survey_source)->toBe('paste')->and($loan->survey_located_by)->toBe($analyst->name)
        ->and((float) $collateral->longitude)->toBe(107.9)->and($collateral->location_source)->toBe('map');

    $this->post(route('surveys.locations.store', $loan), ['target' => 'survey', 'coordinates' => '-6.6, 107.7'])->assertSessionHasNoErrors();
    expect((float) $loan->fresh()->survey_latitude)->toBe(-6.6);

    $this->delete(route('surveys.locations.destroy', $loan), ['target' => 'collateral', 'collateral_id' => $collateral->id])->assertSessionHasNoErrors();
    expect($collateral->fresh()->latitude)->toBeNull()->and($collateral->fresh()->located_by)->toBeNull();
});

it('takes a position from a photo only when the photo carries one', function () {
    [$analyst, $loan] = surveyFile();
    $with = LoanSurveyPhoto::create(['loan_application_id' => $loan->id, 'path' => 'x.jpg', 'latitude' => -6.4, 'longitude' => 107.8, 'source' => 'upload', 'created_by' => 'x']);
    $without = LoanSurveyPhoto::create(['loan_application_id' => $loan->id, 'path' => 'y.jpg', 'source' => 'upload', 'created_by' => 'x']);
    $this->actingAs($analyst);

    $this->post(route('surveys.locations.store', $loan), ['target' => 'survey', 'photo_id' => $without->id])->assertSessionHasErrors('coordinates');
    $this->post(route('surveys.locations.store', $loan), ['target' => 'survey', 'photo_id' => $with->id])->assertSessionHasNoErrors();
    expect($loan->fresh()->survey_source)->toBe('photo')->and((float) $loan->fresh()->survey_latitude)->toBe(-6.4);
});

it('rejects positions that cannot be right', function (array $payload) {
    [$analyst, $loan, $collateral] = surveyFile();
    $this->actingAs($analyst);

    $this->post(route('surveys.locations.store', $loan), ['target' => 'survey', ...$payload])->assertSessionHasErrors();
    expect($loan->fresh()->survey_latitude)->toBeNull();
})->with([
    'short link' => [['coordinates' => 'https://maps.app.goo.gl/AbC123']],
    'outside indonesia' => [['coordinates' => '48.8584, 2.2945']],
    'text' => [['coordinates' => 'near the mosque']],
    'pin outside indonesia' => [['latitude' => 48.8, 'longitude' => 2.3]],
    'nothing' => [[]],
]);

it('only locates collaterals of the file, for the assigned surveyor, until the survey is saved', function () {
    [$analyst, $loan] = surveyFile();
    $stranger = Collateral::create(['collateral_type_code' => '05', 'owner_name' => 'OTHER']);

    $this->actingAs($analyst)->post(route('surveys.locations.store', $loan), ['target' => 'collateral', 'collateral_id' => $stranger->id, 'coordinates' => '-6.4, 107.8'])->assertNotFound();
    expect($stranger->fresh()->latitude)->toBeNull();

    $this->actingAs(User::factory()->create()->assignRole('Staff Analis & Appraisal'))
        ->post(route('surveys.locations.store', $loan), ['target' => 'survey', 'coordinates' => '-6.4, 107.8'])->assertForbidden();

    $loan->update(['status' => LoanStatus::Survey]);
    $this->actingAs($analyst)->post(route('surveys.locations.store', $loan), ['target' => 'survey', 'coordinates' => '-6.4, 107.8'])->assertSessionHas('error');
    expect($loan->fresh()->survey_latitude)->toBeNull();
});

it('shows the survey page with the places of the file and records location changes in the audit trail', function () {
    [$analyst, $loan, $collateral] = surveyFile();
    $this->actingAs($analyst);
    $this->post(route('surveys.locations.store', $loan), ['target' => 'collateral', 'collateral_id' => $collateral->id, 'coordinates' => '-6.4643579, 107.8083288']);

    $this->get(route('surveys.show', $loan))->assertInertia(fn (Assert $page) => $page
        ->component('surveys/show')
        ->where('locations.0.key', 'survey')->where('locations.0.location', null)
        ->where('locations.1.key', 'collateral:'.$collateral->id)
        ->where('locations.1.location.latitude', -6.4643579)
        ->where('locations.1.location.source', 'paste'));

    $row = AuditLog::query()->where('event', 'collaterals.updated')->latest('id')->firstOrFail();
    expect($row->decoded('new_values'))->toHaveKeys(['latitude', 'longitude', 'location_source', 'located_by']);
});

it('marks the survey location from the phone GPS on site', function () {
    [$analyst, $loan] = surveyFile();
    $this->actingAs($analyst)->post(route('surveys.locations.store', $loan), ['target' => 'survey', 'latitude' => -6.4643579, 'longitude' => 107.8083288, 'source' => 'gps', 'accuracy' => 12])->assertSessionHasNoErrors();

    expect($loan->fresh()->survey_source)->toBe('gps')->and((float) $loan->fresh()->survey_longitude)->toBe(107.8083288);
});

it('needs the survey location and a photo of it before the survey can be saved, but nothing for collaterals', function () {
    Storage::fake('public');
    [$analyst, $loan, $collateral] = surveyFile();
    $this->actingAs($analyst);

    $this->post(route('surveys.store', $loan), [])->assertSessionHas('error', 'Tandai lokasi survei sebelum menyimpan.');

    $this->post(route('surveys.locations.store', $loan), ['target' => 'survey', 'coordinates' => '-6.4, 107.8']);
    $this->post(route('surveys.store', $loan), [])->assertSessionHas('error', 'Unggah minimal satu foto lokasi survei sebelum menyimpan.');

    // A collateral photo does not stand in for the survey location photo.
    $this->post(route('surveys.photos.store', $loan), ['photo' => UploadedFile::fake()->image('c.jpg'), 'collateral_id' => $collateral->id])->assertSessionHasNoErrors();
    $this->post(route('surveys.store', $loan), [])->assertSessionHas('error', 'Unggah minimal satu foto lokasi survei sebelum menyimpan.');

    $this->post(route('surveys.photos.store', $loan), ['photo' => UploadedFile::fake()->image('s.jpg')])->assertSessionHasNoErrors();
    $this->post(route('surveys.store', $loan), ['note' => 'done'])->assertSessionHas('success');

    $loan->refresh();
    $survey = $loan->surveys()->firstOrFail();
    expect((float) $survey->latitude)->toBe(-6.4)->and($survey->location_source)->toBe('paste')
        ->and($loan->survey_latitude)->toBeNull()->and($loan->photos()->whereNull('loan_survey_id')->count())->toBe(0)
        ->and($collateral->fresh()->latitude)->toBeNull();
});

it('allows five photos for the survey location and five for each collateral', function () {
    Storage::fake('public');
    [$analyst, $loan, $collateral] = surveyFile();
    $this->actingAs($analyst);

    foreach (range(1, 5) as $i) {
        $this->post(route('surveys.photos.store', $loan), ['photo' => UploadedFile::fake()->image("s{$i}.jpg")])->assertSessionHasNoErrors();
        $this->post(route('surveys.photos.store', $loan), ['photo' => UploadedFile::fake()->image("c{$i}.jpg"), 'collateral_id' => $collateral->id])->assertSessionHasNoErrors();
    }

    $this->post(route('surveys.photos.store', $loan), ['photo' => UploadedFile::fake()->image('s6.jpg')])->assertSessionHas('error');
    $this->post(route('surveys.photos.store', $loan), ['photo' => UploadedFile::fake()->image('c6.jpg'), 'collateral_id' => $collateral->id])->assertSessionHas('error');
    expect($loan->photos()->count())->toBe(10);

    $stranger = Collateral::create(['collateral_type_code' => '05']);
    $this->post(route('surveys.photos.store', $loan), ['photo' => UploadedFile::fake()->image('x.jpg'), 'collateral_id' => $stranger->id])->assertNotFound();
});

it('shows on the survey list what each file still needs, so the location can be marked from there', function () {
    Storage::fake('public');
    [$analyst, $loan] = surveyFile();
    $this->actingAs($analyst);

    $this->get(route('surveys.index'))->assertInertia(fn (Assert $page) => $page
        ->where('loans.0.has_location', false)->where('loans.0.located_at', null)->where('loans.0.survey_photos', 0));

    $this->post(route('surveys.locations.store', $loan), ['target' => 'survey', 'latitude' => -6.46, 'longitude' => 107.8, 'source' => 'gps']);
    $this->post(route('surveys.photos.store', $loan), ['photo' => UploadedFile::fake()->image('s.jpg')]);
    // A collateral photo is not a photo of the survey location.
    $this->post(route('surveys.photos.store', $loan), ['photo' => UploadedFile::fake()->image('c.jpg'), 'collateral_id' => $loan->collaterals()->firstOrFail()->id]);

    $this->get(route('surveys.index'))->assertInertia(fn (Assert $page) => $page
        ->where('loans.0.has_location', true)->where('loans.0.survey_photos', 1)->whereType('loans.0.located_at', 'string'));
});

it('lets the surveyor cancel from the survey page, discarding what that visit produced and keeping the history', function () {
    Storage::fake('public');
    [$analyst, $loan, $collateral] = surveyFile();
    $loan->update(['supervisor_id' => User::factory()->create()->assignRole('Kepala Seksi Analis')->id]);
    $this->actingAs($analyst);
    $this->post(route('surveys.locations.store', $loan), ['target' => 'survey', 'coordinates' => '-6.4, 107.8']);
    $this->post(route('surveys.locations.store', $loan), ['target' => 'collateral', 'collateral_id' => $collateral->id, 'coordinates' => '-6.5, 107.9']);
    $this->post(route('surveys.photos.store', $loan), ['photo' => UploadedFile::fake()->image('s.jpg')]);
    $path = $loan->photos()->firstOrFail()->path;

    $this->post(route('scheduling.cancel', $loan), ['return' => 'surveys'])->assertSessionHasErrors('reason');
    expect($loan->fresh()->status)->toBe(LoanStatus::Scheduling);

    $this->post(route('scheduling.cancel', $loan), ['reason' => 'Customer is away', 'return' => 'surveys'])
        ->assertRedirect(route('surveys.index'))->assertSessionHas('success');

    $loan->refresh();
    expect($loan->status)->toBe(LoanStatus::Submitted)->and($loan->surveyor_id)->toBeNull()->and($loan->survey_latitude)->toBeNull()
        ->and($loan->photos()->count())->toBe(0)
        ->and($collateral->fresh()->latitude)->not->toBeNull()
        ->and($loan->schedules()->where('action', LoanSchedule::ACTION_CANCEL)->value('reason'))->toBe('Customer is away');
    Storage::disk('public')->assertMissing($path);

    // The file is no longer theirs: the survey page is closed to them.
    $this->get(route('surveys.show', $loan))->assertForbidden();
});

it('keeps going back for the scheduling list and refuses a return target it does not know', function () {
    [$analyst, $loan] = surveyFile();
    $this->actingAs($analyst)->post(route('scheduling.cancel', $loan), ['reason' => 'x', 'return' => 'https://evil.test'])->assertSessionHasErrors('return');
    expect($loan->fresh()->status)->toBe(LoanStatus::Scheduling);

    $this->post(route('scheduling.cancel', $loan), ['reason' => 'x'])->assertRedirect()->assertSessionHas('success');
    expect($loan->fresh()->status)->toBe(LoanStatus::Submitted);
});

it('ignores any position sent along with a photo: the uploading device is not where the survey took place', function () {
    Storage::fake('public');
    [$analyst, $loan] = surveyFile();

    $this->actingAs($analyst)->post(route('surveys.photos.store', $loan), [
        'photo' => UploadedFile::fake()->image('s.jpg'), 'latitude' => -6.9, 'longitude' => 107.6,
    ])->assertSessionHasNoErrors();

    $photo = $loan->photos()->firstOrFail();
    expect($photo->latitude)->toBeNull()->and($photo->longitude)->toBeNull()->and($loan->fresh()->survey_latitude)->toBeNull();
});
