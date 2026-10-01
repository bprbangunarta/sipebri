<?php

use App\Audit\AuditLog;
use App\Enums\LoanStatus;
use App\Models\CommitteePath;
use App\Models\CommitteeTier;
use App\Models\Installment;
use App\Models\LoanApplication;
use App\Models\Method;
use App\Models\Office;
use App\Models\Product;
use App\Models\ProductParameter;
use App\Models\User;
use App\Support\CommitteeMembers;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;

const KASI_A_NIK = '3201000000000011';

/**
 * Roles, a committee path with its tiers, and two section heads (one of them with a NIK on record), a department head and a director.
 *
 * @return array<string, mixed>
 */
function committeeSetup(): array
{
    test()->seed(RoleSeeder::class);

    $product = Product::create(['code' => '01', 'alias' => 'KRU', 'name' => 'GENERAL', 'is_active' => true]);
    $path = CommitteePath::create(['product_id' => $product->id, 'mechanism' => 'plafon']);
    foreach (['Kepala Seksi Analis', 'Kepala Bagian Analis', 'Direktur Bisnis', 'Direktur Utama'] as $i => $role) {
        CommitteeTier::create(['committee_path_id' => $path->id, 'sort' => $i + 1, 'role' => $role, 'can_escalate' => true, 'can_approve' => true]);
    }

    $kasiA = User::factory()->create(['name' => 'Kasi A'])->assignRole('Kepala Seksi Analis');
    $kasiA->setNik(KASI_A_NIK, 'manual');

    return [
        'product' => $product, 'path' => $path, 'kasiA' => $kasiA,
        'kasiB' => User::factory()->create(['name' => 'Kasi B'])->assignRole('Kepala Seksi Analis'),
        'kabag' => User::factory()->create(['name' => 'Kabag'])->assignRole('Kepala Bagian Analis'),
        'dirut' => User::factory()->create(['name' => 'Dirut'])->assignRole('Direktur Utama'),
        'outsider' => User::factory()->create(['name' => 'Teller One'])->assignRole('Teller'),
    ];
}

it('derives the committee from the roles that decide in a tier, and lists them for Super Admin only', function () {
    $s = committeeSetup();

    expect(CommitteeMembers::query()->pluck('name')->sort()->values()->all())->toBe(['Dirut', 'Kabag', 'Kasi A', 'Kasi B'])
        ->and(CommitteeMembers::isMember($s['outsider']))->toBeFalse();

    $this->actingAs(superAdmin())->get(route('committees.members'))->assertInertia(fn (Assert $page) => $page
        ->component('committees/members')->has('members.data', 4)->where('withoutNik', 3)
        ->where('members.data.0.name', 'Dirut')->where('members.data.0.nik', null));

    $this->actingAs($s['outsider'])->get(route('committees.members'))->assertForbidden();
});

it('shows a NIK only masked, keeps it encrypted, and never writes it to the audit trail', function () {
    $s = committeeSetup();

    $this->actingAs(superAdmin())->get(route('committees.members', ['search' => 'Kasi A']))->assertInertia(fn (Assert $page) => $page
        ->where('members.data.0.nik', '••••••••••••0011'));

    expect(DB::table('users')->where('id', $s['kasiA']->id)->value('nik'))->not->toContain(KASI_A_NIK)
        ->and(json_encode(User::find($s['kasiA']->id)))->not->toContain(KASI_A_NIK)
        ->and(AuditLog::query()->get()->contains(fn (AuditLog $l) => str_contains((string) $l->new_values.(string) $l->old_values.(string) $l->context, KASI_A_NIK)))->toBeFalse()
        ->and(AuditLog::query()->where('event', 'users.nik_changed')->exists())->toBeTrue();
});

it('lets a Super Admin type, replace and remove a NIK, validating it', function () {
    $s = committeeSetup();
    $this->actingAs(superAdmin());

    $this->put(route('committees.members.update', $s['kabag']), ['nik' => '123'])->assertSessionHasErrors('nik');
    $this->put(route('committees.members.update', $s['kabag']), ['nik' => KASI_A_NIK])->assertSessionHasErrors('nik'); // belongs to Kasi A
    $this->put(route('committees.members.update', $s['outsider']), ['nik' => '3201000000000022'])->assertNotFound(); // not a member

    $this->put(route('committees.members.update', $s['kabag']), ['nik' => '3201000000000022'])->assertSessionHasNoErrors();
    expect($s['kabag']->fresh()->nik)->toBe('3201000000000022')->and($s['kabag']->fresh()->nik_source)->toBe('manual');

    $this->delete(route('committees.members.destroy', $s['kabag']))->assertSessionHasNoErrors();
    expect($s['kabag']->fresh()->nik)->toBeNull()->and($s['kabag']->fresh()->nik_hash)->toBeNull();
});

it('takes the NIK from Codex when it sends one, and keeps a typed NIK when it sends none', function () {
    committeeSetup();
    config(['services.codex.endpoint' => 'https://codex.test', 'services.codex.token' => 'test-token']);

    fakeCodex(['nik' => '3201000000000033', 'role' => 'Kepala Bagian Analis']);
    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x']);
    $user = User::findOrFail(30);
    expect($user->nik)->toBe('3201000000000033')->and($user->nik_source)->toBe('codex')->and(CommitteeMembers::findByNik('3201000000000033')?->id)->toBe(30);

    $this->post(route('logout'));
    fakeCodex(['role' => 'Kepala Bagian Analis']); // Codex sends no NIK this time
    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x']);
    expect(User::findOrFail(30)->nik)->toBe('3201000000000033');

    $this->post(route('logout'));
    fakeCodex(['nik' => 'not-a-number', 'role' => 'Kepala Bagian Analis']);
    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x']);
    expect(User::findOrFail(30)->nik)->toBe('3201000000000033');
});

it('recognises an applicant who is a committee member from the national ID', function () {
    $s = committeeSetup();
    fakeCustomers([KASI_A_NIK => 'Kasi A', '3201000000000044' => 'Somebody']);
    $this->actingAs(loanOfficerFor());

    $this->getJson(route('loan-applications.lookup', ['nik' => KASI_A_NIK]))->assertOk()
        ->assertJsonPath('committee_member.id', $s['kasiA']->id)->assertJsonPath('committee_member.role', 'Kepala Seksi Analis');
    $this->getJson(route('loan-applications.lookup', ['nik' => '3201000000000044']))->assertOk()->assertJsonPath('committee_member', null);
});

it('flags the file from the start, and a detected flag cannot be switched off', function () {
    $s = committeeSetup();
    fakeCustomers([KASI_A_NIK => 'Kasi A', '3201000000000044' => 'Somebody']);
    $officer = loanOfficerFor();
    $this->actingAs($officer);

    // Detected by NIK: even if the officer tries to name somebody else, the system's finding wins.
    $this->post(route('loan-applications.store'), ['nik' => KASI_A_NIK, 'committee_conflict_user_id' => $s['kabag']->id])->assertSessionHasNoErrors();
    $loan = LoanApplication::query()->where('nik', KASI_A_NIK)->firstOrFail();
    expect($loan->committee_conflict_user_id)->toBe($s['kasiA']->id)->and($loan->committee_conflict_source)->toBe('nik');

    $this->put(route('loan-applications.update', $loan), committeeLoanPayload($s, ['committee_conflict_user_id' => null]))->assertSessionHasNoErrors();
    expect($loan->fresh()->committee_conflict_user_id)->toBe($s['kasiA']->id);

    // Not recognised (no NIK on record), so the officer flags the member by hand; only members can be flagged.
    $this->post(route('loan-applications.store'), ['nik' => '3201000000000044', 'committee_conflict_user_id' => $s['outsider']->id])->assertSessionHasErrors('committee_conflict_user_id');
    $this->post(route('loan-applications.store'), ['nik' => '3201000000000044', 'committee_conflict_user_id' => $s['dirut']->id])->assertSessionHasNoErrors();
    $manual = LoanApplication::query()->where('nik', '3201000000000044')->firstOrFail();
    expect($manual->committee_conflict_user_id)->toBe($s['dirut']->id)->and($manual->committee_conflict_source)->toBe('manual');

    $this->put(route('loan-applications.update', $manual), committeeLoanPayload($s, ['committee_conflict_user_id' => $s['kabag']->id]))->assertSessionHasNoErrors();
    expect($manual->fresh()->committee_conflict_user_id)->toBe($s['kabag']->id);
    $this->put(route('loan-applications.update', $manual), committeeLoanPayload($s, ['committee_conflict_user_id' => null]))->assertSessionHasNoErrors();
    expect($manual->fresh()->committee_conflict_user_id)->toBeNull();
});

it('leaves the other section head when a section head applies, and refuses the applicant as section head', function () {
    $s = committeeSetup();
    fakeCustomers([KASI_A_NIK => 'Kasi A']);
    $this->actingAs(loanOfficerFor());
    $this->post(route('loan-applications.store'), ['nik' => KASI_A_NIK]);
    $loan = LoanApplication::query()->firstOrFail();

    $this->get(route('loan-applications.show', $loan))->assertInertia(fn (Assert $page) => $page
        ->where('loan.committee_conflict.name', 'Kasi A')->where('loan.committee_conflict.source', 'nik')
        ->has('references.supervisors', 1)->where('references.supervisors.0.value', $s['kasiB']->id));

    $this->put(route('loan-applications.update', $loan), committeeLoanPayload($s, ['supervisor_id' => $s['kasiA']->id]))
        ->assertSessionHasErrors(['supervisor_id' => 'The section head cannot be the applicant. Choose another section head.']);
    $this->put(route('loan-applications.update', $loan), committeeLoanPayload($s, ['supervisor_id' => $s['kasiB']->id]))->assertSessionHasNoErrors();
    expect($loan->fresh()->supervisor_id)->toBe($s['kasiB']->id);
});

it('does not let the applicant survey their own file', function () {
    $s = committeeSetup();
    $s['kasiA']->update(['name' => 'Analyst Kasi']);
    $loan = LoanApplication::create([
        'application_code' => '00700001', 'application_date' => now(), 'status' => LoanStatus::Submitted->value, 'nik' => KASI_A_NIK, 'full_name' => 'Kasi A',
        'supervisor_id' => $s['kasiB']->id, 'created_by' => $s['kasiB']->id, 'committee_conflict_user_id' => $s['kasiA']->id, 'committee_conflict_source' => 'nik',
    ]);
    $kasi = $s['kasiB']->givePermissionTo(['scheduling.manage', 'scheduling.view']);

    $this->actingAs($kasi)->post(route('scheduling.store', $loan), ['survey_date' => now()->toDateString(), 'surveyor_id' => $s['kasiA']->id])->assertSessionHasErrors('surveyor_id');
});

/** An officer allowed to open applications. */
function loanOfficerFor(): User
{
    return userWith(['loan-applications.view', 'loan-applications.manage'], 'AO Committee Test');
}

/**
 * A valid application payload for the product of the committee setup.
 *
 * @param  array<string, mixed>  $s
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function committeeLoanPayload(array $s, array $overrides = []): array
{
    // Created once per test (the database is reset between tests).
    $method = Method::firstOrCreate(['code' => '10'], ['name' => 'FLAT']);
    $installment = Installment::firstOrCreate(['code' => '3'], ['name' => 'MONTHLY', 'period_months' => 1]);
    $office = Office::firstOrCreate(['code' => '00'], ['alias' => 'PMK', 'name' => 'Pamanukan']);
    ProductParameter::firstOrCreate(['product_id' => $s['product']->id], [
        'min_amount' => 1_000_000, 'max_amount' => 50_000_000, 'min_tenor' => 1, 'max_tenor' => 24,
        'allowed_method_ids' => [$method->id], 'allowed_installment_ids' => [$installment->id],
    ]);
    $refs = ['method' => $method, 'installment' => $installment, 'office' => $office];

    return array_merge([
        'application_date' => now()->toDateString(), 'product_id' => $s['product']->id, 'committee_path_id' => $s['path']->id,
        'requested_amount' => 10_000_000, 'requested_tenor' => 12, 'method_id' => $refs['method']->id, 'installment_id' => $refs['installment']->id,
        'interest_rate' => 13, 'usage_type' => 'WORKING CAPITAL', 'office_id' => $refs['office']->id, 'supervisor_id' => $s['kasiB']->id,
    ], $overrides);
}
