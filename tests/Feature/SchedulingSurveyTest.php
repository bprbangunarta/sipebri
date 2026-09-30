<?php

use App\Enums\LoanStatus;
use App\Models\AppNotification;
use App\Models\LoanApplication;
use App\Models\LoanSchedule;
use App\Models\LoanSurvey;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

function submittedLoan(?User $supervisor = null, string $alias = 'KRU'): LoanApplication
{
    test()->seed(RoleSeeder::class);
    $product = Product::firstOrCreate(['alias' => $alias], ['code' => $alias, 'name' => $alias, 'is_active' => true]);

    return LoanApplication::create([
        'application_code' => LoanApplication::nextCode(), 'application_date' => now(), 'status' => LoanStatus::Submitted,
        'nik' => '3201000000000001', 'full_name' => 'Siti', 'product_id' => $product->id, 'supervisor_id' => $supervisor?->id,
    ]);
}

function roleUser(string $role): User
{
    test()->seed(RoleSeeder::class);

    return User::factory()->create()->assignRole($role);
}

it('schedules a survey, keeps an append-only history and notifies the surveyor', function () {
    $kasi = roleUser('Kepala Seksi Analis');
    $analyst = roleUser('Staff Analis & Appraisal');
    $loan = submittedLoan($kasi);
    $this->actingAs($kasi);

    $this->post(route('scheduling.store', $loan), ['survey_date' => now()->subDay()->toDateString(), 'surveyor_id' => $analyst->id])->assertSessionHasErrors('survey_date');
    $this->post(route('scheduling.store', $loan), ['survey_date' => now()->toDateString(), 'surveyor_id' => roleUser('Kepala Bagian Analis')->id])->assertSessionHasErrors('surveyor_id');

    $this->post(route('scheduling.store', $loan), ['survey_date' => now()->toDateString(), 'surveyor_id' => $analyst->id, 'note' => 'bring ID'])->assertSessionHasNoErrors();
    expect($loan->fresh()->status)->toBe(LoanStatus::Scheduling)->and($loan->fresh()->surveyor_id)->toBe($analyst->id)
        ->and(AppNotification::where('user_id', $analyst->id)->count())->toBe(1);

    $this->post(route('scheduling.store', $loan), ['survey_date' => now()->addDay()->toDateString(), 'surveyor_id' => $analyst->id])->assertSessionHasNoErrors();
    expect($loan->schedules()->pluck('action')->all())->toBe([LoanSchedule::ACTION_SCHEDULE, LoanSchedule::ACTION_RESCHEDULE]);
});

it('only lets scheduling files at an open stage and lists a section head\'s own files', function () {
    $kasi = roleUser('Kepala Seksi Analis');
    $mine = submittedLoan($kasi);
    $other = submittedLoan(roleUser('Kepala Seksi Analis'));
    $draft = tap(submittedLoan($kasi))->update(['status' => LoanStatus::Draft]);
    $this->actingAs($kasi);

    $this->post(route('scheduling.store', $draft), ['survey_date' => now()->toDateString(), 'surveyor_id' => 1])->assertSessionHas('error');
    $this->get(route('scheduling.index'))->assertInertia(fn (Assert $page) => $page->has('loans.data', 1)->where('loans.data.0.id', $mine->id));
    $this->get(route('scheduling.index', ['scope' => 'all']))->assertInertia(fn (Assert $page) => $page->has('loans.data', 2));
});

it('lets the assigned surveyor cancel a schedule, warning after the limit', function () {
    $kasi = roleUser('Kepala Seksi Analis');
    $analyst = roleUser('Staff Analis & Appraisal');
    $loan = submittedLoan($kasi);

    foreach (range(1, 3) as $i) {
        $this->actingAs($kasi)->post(route('scheduling.store', $loan), ['survey_date' => now()->toDateString(), 'surveyor_id' => $analyst->id]);
    }

    $this->actingAs(roleUser('Staff Analis & Appraisal'))->post(route('scheduling.cancel', $loan), ['reason' => 'x'])->assertForbidden();
    $this->actingAs($analyst)->post(route('scheduling.cancel', $loan), [])->assertSessionHasErrors('reason');
    $this->actingAs($analyst)->post(route('scheduling.cancel', $loan), ['reason' => 'customer away'])->assertSessionHas('success');

    expect($loan->fresh()->status)->toBe(LoanStatus::Submitted)->and($loan->fresh()->surveyor_id)->toBeNull()
        ->and($loan->schedules()->reorder('id', 'desc')->first()->action)->toBe(LoanSchedule::ACTION_CANCEL)
        ->and(AppNotification::where('user_id', $kasi->id)->where('level', 'warning')->exists())->toBeTrue();
});

it('voids an application with a reason', function () {
    $kasi = roleUser('Kepala Seksi Analis');
    $loan = submittedLoan($kasi);
    $this->actingAs($kasi);

    $this->post(route('scheduling.void', $loan), [])->assertSessionHasErrors('reason');
    $this->post(route('scheduling.void', $loan), ['reason' => 'customer withdrew'])->assertSessionHas('success');
    expect($loan->fresh()->status)->toBe(LoanStatus::Cancelled);
});

it('skips the field survey for the walk-in product', function () {
    $kasi = roleUser('Kepala Seksi Analis');
    $teller = roleUser('Teller');
    $loan = submittedLoan($kasi, 'KTA');
    $this->actingAs($kasi);

    $this->post(route('scheduling.store', $loan), ['survey_date' => now()->toDateString(), 'surveyor_id' => roleUser('Staff Analis & Appraisal')->id])->assertSessionHasErrors('surveyor_id');
    $this->post(route('scheduling.store', $loan), ['survey_date' => now()->toDateString(), 'surveyor_id' => $teller->id])->assertSessionHasNoErrors();

    expect($loan->fresh()->status)->toBe(LoanStatus::Survey);
});

it('runs a survey: today only, photos with coordinates, then locks', function () {
    Storage::fake('public');
    $analyst = roleUser('Staff Analis & Appraisal');
    $loan = submittedLoan(roleUser('Kepala Seksi Analis'));
    $loan->update(['status' => LoanStatus::Scheduling, 'surveyor_id' => $analyst->id, 'survey_date' => now()->toDateString()]);
    $tomorrow = tap(submittedLoan())->update(['status' => LoanStatus::Scheduling, 'surveyor_id' => $analyst->id, 'survey_date' => now()->addDay()->toDateString()]);
    $this->actingAs($analyst);

    $this->get(route('surveys.index'))->assertInertia(fn (Assert $page) => $page->has('loans', 1)->where('loans.0.id', $loan->id));
    $this->actingAs(roleUser('Staff Analis & Appraisal'))->get(route('surveys.show', $loan))->assertForbidden();
    $this->actingAs($analyst);

    $this->post(route('surveys.store', $loan), ['note' => 'ok'])->assertSessionHas('error'); // no photo yet
    $this->post(route('surveys.photos.store', $loan), ['photo' => UploadedFile::fake()->image('a.jpg'), 'latitude' => 999, 'longitude' => 0])->assertSessionHasErrors('latitude');
    $this->post(route('surveys.photos.store', $loan), ['photo' => UploadedFile::fake()->create('a.pdf', 10), 'latitude' => -6.2, 'longitude' => 106.8])->assertSessionHasErrors('photo');

    foreach (range(1, 5) as $i) {
        $this->post(route('surveys.photos.store', $loan), ['photo' => UploadedFile::fake()->image("p{$i}.jpg"), 'latitude' => -6.2, 'longitude' => 106.8])->assertSessionHasNoErrors();
    }
    $this->post(route('surveys.photos.store', $loan), ['photo' => UploadedFile::fake()->image('p6.jpg'), 'latitude' => -6.2, 'longitude' => 106.8])->assertSessionHas('error');
    expect($loan->photos()->count())->toBe(5);

    $this->post(route('surveys.store', $loan), ['note' => 'looks fine'])->assertSessionHas('success');
    $loan->refresh();
    expect($loan->status)->toBe(LoanStatus::Survey)->and(LoanSurvey::count())->toBe(1)->and($loan->photos()->whereNull('loan_survey_id')->count())->toBe(0);

    $this->post(route('surveys.photos.store', $loan), ['photo' => UploadedFile::fake()->image('late.jpg'), 'latitude' => 1, 'longitude' => 1])->assertSessionHas('error');
    $this->delete(route('surveys.photos.destroy', [$loan, $loan->photos()->first()]))->assertSessionHas('error');
    $this->post(route('surveys.store', $loan), [])->assertSessionHas('error');
    Storage::disk('public')->assertExists($loan->photos()->first()->path);
    expect($tomorrow->fresh()->status)->toBe(LoanStatus::Scheduling);
});

it('escalates the required surveyor role after a survey was judged insufficient', function () {
    $kasi = roleUser('Kepala Seksi Analis');
    $analyst = roleUser('Staff Analis & Appraisal');
    $loan = submittedLoan($kasi);
    $loan->update(['status' => LoanStatus::Survey]);
    LoanSurvey::create(['loan_application_id' => $loan->id, 'sequence' => 1, 'created_by' => 'x']);
    $this->actingAs($kasi);

    $this->post(route('scheduling.store', $loan), ['survey_date' => now()->toDateString(), 'surveyor_id' => $analyst->id])->assertSessionHasErrors('surveyor_id');
    $this->post(route('scheduling.store', $loan), ['survey_date' => now()->toDateString(), 'surveyor_id' => $kasi->id])->assertSessionHasNoErrors();

    expect($loan->schedules()->reorder('id', 'desc')->first()->action)->toBe(LoanSchedule::ACTION_RESURVEY)->and($loan->fresh()->status)->toBe(LoanStatus::Scheduling);
});

it('lists only surveyed or walk-in files assigned to the user on the analysis page', function () {
    $analyst = roleUser('Staff Analis & Appraisal');
    $ready = submittedLoan(roleUser('Kepala Seksi Analis'));
    $ready->update(['status' => LoanStatus::Survey, 'surveyor_id' => $analyst->id, 'survey_date' => now()]);
    LoanSurvey::create(['loan_application_id' => $ready->id, 'sequence' => 1, 'created_by' => 'x']);
    $notYet = tap(submittedLoan())->update(['status' => LoanStatus::Scheduling, 'surveyor_id' => $analyst->id]);
    $someoneElses = tap(submittedLoan())->update(['status' => LoanStatus::Survey, 'surveyor_id' => roleUser('Staff Analis & Appraisal')->id]);

    $this->actingAs($analyst)->get(route('analysis.index'))
        ->assertInertia(fn (Assert $page) => $page->component('analysis/index')->has('loans.data', 1)->where('loans.data.0.id', $ready->id)->where('loans.data.0.surveyed', true));
    $this->get(route('analysis.index', ['search' => 'nobody']))->assertInertia(fn (Assert $page) => $page->has('loans.data', 0));

    $this->actingAs(userWith(['dashboard.view'], 'Nobody'))->get(route('analysis.index'))->assertForbidden();
    expect($notYet->fresh()->status)->toBe(LoanStatus::Scheduling)->and($someoneElses->fresh()->status)->toBe(LoanStatus::Survey);
});
