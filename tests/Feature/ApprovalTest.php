<?php

use App\Enums\LoanStatus;
use App\Models\AnalysisFinance;
use App\Models\AnalysisFiveC;
use App\Models\AnalysisMemorandum;
use App\Models\AppNotification;
use App\Models\LoanAnalysis;
use App\Models\LoanApplication;
use App\Models\Method;
use App\Models\Product;
use App\Models\User;
use App\Support\CreditAnalysis\RepaymentCapacity;
use Database\Seeders\CommitteeSeeder;
use Database\Seeders\RoleSeeder;
use Inertia\Testing\AssertableInertia as Assert;

/**
 * The committee route: staff (individual, up to 10 million), section head (up to 35 million), department head (up to 100
 * million), business director (up to 300 million), president director (above).
 *
 * @return array<string, User>
 */
function committeePeople(): array
{
    Product::firstOrCreate(['alias' => 'KRU'], ['code' => 'KRU', 'name' => 'KRU', 'is_active' => true]);
    test()->seed([RoleSeeder::class, CommitteeSeeder::class]);

    return [
        'analyst' => User::factory()->create(['name' => 'Analyst'])->assignRole('Staff Analis & Appraisal'),
        'kasi' => User::factory()->create(['name' => 'Kasi'])->assignRole('Kepala Seksi Analis'),
        'kabag' => User::factory()->create(['name' => 'Kabag'])->assignRole('Kepala Bagian Analis'),
        'dirbis' => User::factory()->create(['name' => 'Dirbis'])->assignRole('Direktur Bisnis'),
        'dirut' => User::factory()->create(['name' => 'Dirut'])->assignRole('Direktur Utama'),
    ];
}

/** A file whose analysis is complete, proposing the given amount, ready to be sent to the committee. */
function analysedFor(array $people, int $amount, array $loanAttributes = []): LoanApplication
{
    Product::firstOrCreate(['alias' => 'KRU'], ['code' => 'KRU', 'name' => 'KRU', 'is_active' => true]);

    $loan = LoanApplication::create([
        'application_code' => LoanApplication::nextCode(), 'application_date' => now(), 'status' => LoanStatus::Survey,
        'nik' => '3201000000000001', 'full_name' => 'Siti', 'product_id' => Product::where('alias', 'KRU')->value('id'),
        'requested_amount' => $amount, 'requested_tenor' => 12, 'method_id' => Method::firstOrCreate(['code' => 'M1'], ['name' => 'ANUITAS'])->id,
        'surveyor_id' => $people['analyst']->id, 'supervisor_id' => $people['kasi']->id, ...$loanAttributes,
    ]);

    $analysis = LoanAnalysis::begin($loan);
    $analysis->businesses()->create(['type' => 'service', 'code' => 'AUJ'.str_pad((string) $loan->id, 5, '0', STR_PAD_LEFT), 'name' => 'X', 'service_income' => 4_000_000, 'monthly_income' => 4_000_000, 'net_profit' => 4_000_000]);
    AnalysisFinance::create(['loan_analysis_id' => $analysis->id, 'cost_staple' => 1_000_000]);
    AnalysisFiveC::create(['loan_analysis_id' => $analysis->id, 'capital_source' => 3]);
    AnalysisMemorandum::create(['loan_analysis_id' => $analysis->id, 'proposed_amount' => $amount, 'term_months' => 12, 'interest_rate' => 12]);

    return $loan;
}

function submitAnalysis(User $analyst, LoanApplication $loan): void
{
    test()->actingAs($analyst)->post(route('credit-analysis.submit', $loan))->assertSessionMissing('error');
}

function decision(string $decision, array $extra = []): array
{
    return ['decision' => $decision, 'method_id' => Method::first()->id, 'amount' => 20_000_000, 'tenor' => 12, 'interest_rate' => 12, 'provision_rate' => 1, 'admin_rate' => 1, ...$extra];
}

it('works out the most the applicant can repay and the loan as a share of it', function () {
    // 3.000.000 a month, 70% may go to the installment: 2.100.000 a month, 12% a year over 12 months
    expect(RepaymentCapacity::maxAmount(3_000_000, 70.0, 12.0, 12, 'FLATE'))->toBe(22_500_000)
        ->and(RepaymentCapacity::maxAmount(3_000_000, 70.0, 12.0, 12, 'ANUITAS'))->toBe(23_635_663)
        ->and(RepaymentCapacity::maxAmount(3_000_000, 70.0, 0.0, 12, 'FLATE'))->toBe(25_200_000)
        ->and(RepaymentCapacity::maxAmount(0, 70.0, 12.0, 12, 'FLATE'))->toBe(0)
        ->and(RepaymentCapacity::ratio(11_250_000, 22_500_000))->toBe(50.0)
        ->and(RepaymentCapacity::ratio(1, 0))->toBe(0.0);
});

it('shows the approvals page to people with the permission and lists it in the menu', function () {
    $user = userWith(['dashboard.view', 'approvals.view']);

    $this->actingAs($user)->get('/approvals')->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('approvals/index')
        ->where('navigation.0.items', fn ($items) => collect($items)->contains('label', 'Persetujuan')));
});

it('keeps the approvals page away from people without the permission', function () {
    $this->actingAs(userWith(['dashboard.view']))->get('/approvals')->assertForbidden();
});

it('puts a small file with the analyst, who decides it alone', function () {
    $people = committeePeople();
    $loan = analysedFor($people, 8_000_000);
    submitAnalysis($people['analyst'], $loan);

    expect($loan->fresh()->status)->toBe(LoanStatus::Committee)
        ->and($loan->approvals()->pluck('role')->all())->toBe(['Staff Analis & Appraisal'])
        ->and($loan->approvals->first()->is_individual)->toBeTrue()->and($loan->approvals->first()->isPending())->toBeTrue();

    // A section head has no say on a file the analyst decides; the analyst does.
    $this->actingAs($people['kasi'])->post(route('approvals.decide', $loan), decision('approve'))->assertForbidden();
    $this->actingAs($people['analyst'])->post(route('approvals.decide', $loan), decision('approve', ['amount' => 8_000_000]))->assertRedirect(route('approvals.index'));

    $loan->refresh();
    expect($loan->status)->toBe(LoanStatus::Approved)->and($loan->approved_amount)->toBe(8_000_000)->and($loan->decided_by)->toBe($people['analyst']->id);
});

it('sends a bigger file up through every tier until the one whose limit covers it', function () {
    $people = committeePeople();
    $loan = analysedFor($people, 50_000_000);
    submitAnalysis($people['analyst'], $loan);

    // staff passed it on by submitting; section head, department head decide
    $steps = $loan->approvals()->get();
    expect($steps->pluck('role')->all())->toBe(['Staff Analis & Appraisal', 'Kepala Seksi Analis', 'Kepala Bagian Analis'])
        ->and($steps[0]->decision)->toBe('forward')->and($steps[0]->decided_by)->toBe($people['analyst']->id)
        ->and(AppNotification::where('user_id', $people['kasi']->id)->where('title', 'Berkas masuk komite kredit')->exists())->toBeTrue();

    // The department head cannot act before the section head.
    $this->actingAs($people['kabag'])->post(route('approvals.decide', $loan), decision('approve', ['amount' => 50_000_000]))->assertForbidden();

    // The section head's limit is 35 million: approving 50 million is refused, passing it on is not.
    $this->actingAs($people['kasi'])->post(route('approvals.decide', $loan), decision('approve', ['amount' => 50_000_000]))->assertSessionHasErrors('decision');
    $this->post(route('approvals.decide', $loan), decision('forward', ['amount' => 50_000_000]))->assertRedirect();
    expect(AppNotification::where('user_id', $people['kabag']->id)->where('title', 'Berkas menunggu keputusan Anda')->exists())->toBeTrue();

    $this->actingAs($people['kabag'])->post(route('approvals.decide', $loan), decision('approve', ['amount' => 45_000_000, 'tenor' => 24, 'note' => 'reduced']))->assertRedirect();

    $loan->refresh();
    expect($loan->status)->toBe(LoanStatus::Approved)->and($loan->approved_amount)->toBe(45_000_000)->and($loan->approved_tenor)->toBe(24)
        ->and($loan->decision_note)->toBe('reduced')->and($loan->approvals()->pluck('decision')->all())->toBe(['forward', 'forward', 'approve']);
});

it('can reject and cancel, and a file that is decided cannot be decided again', function () {
    $people = committeePeople();
    $loan = analysedFor($people, 20_000_000);
    submitAnalysis($people['analyst'], $loan);

    $this->actingAs($people['kasi'])->post(route('approvals.decide', $loan), decision('reject', ['note' => 'weak']))->assertRedirect();
    expect($loan->fresh()->status)->toBe(LoanStatus::Rejected)->and($loan->fresh()->approved_amount)->toBe(0);
    $this->post(route('approvals.decide', $loan), decision('approve'))->assertNotFound();

    $other = analysedFor($people, 20_000_000);
    submitAnalysis($people['analyst'], $other);
    $this->actingAs($people['kasi'])->post(route('approvals.decide', $other), decision('cancel'))->assertRedirect();
    expect($other->fresh()->status)->toBe(LoanStatus::Cancelled);
});

it('takes the highest tier below when the top committee member is the applicant', function () {
    $people = committeePeople();
    $loan = analysedFor($people, 500_000_000, ['committee_conflict_user_id' => $people['dirut']->id, 'committee_conflict_source' => 'manual']);
    submitAnalysis($people['analyst'], $loan);

    expect($loan->approvals()->pluck('role')->all())->toBe(['Staff Analis & Appraisal', 'Kepala Seksi Analis', 'Kepala Bagian Analis', 'Direktur Bisnis'])
        ->and($loan->fresh()->committee_exception)->toContain('Direktur Bisnis memutus meski plafon melebihi batasnya');

    foreach (['kasi', 'kabag'] as $who) {
        $this->actingAs($people[$who])->post(route('approvals.decide', $loan), decision('forward', ['amount' => 500_000_000]))->assertRedirect();
    }

    // The business director may approve 500 million although the limit is 300 million, and the applicant may not decide at all.
    $this->actingAs($people['dirut'])->post(route('approvals.decide', $loan), decision('approve', ['amount' => 500_000_000]))->assertForbidden();
    $this->actingAs($people['dirbis'])->post(route('approvals.decide', $loan), decision('approve', ['amount' => 500_000_000]))->assertRedirect();
    expect($loan->fresh()->status)->toBe(LoanStatus::Approved);
});

it('does not let the file go to the committee when there is no route for its product', function () {
    $people = committeePeople();
    $loan = analysedFor($people, 20_000_000);
    $loan->update(['product_id' => Product::create(['code' => 'ZZ', 'alias' => 'ZZ', 'name' => 'ZZ', 'is_active' => true])->id]);

    $this->actingAs($people['analyst'])->post(route('credit-analysis.submit', $loan))->assertSessionHas('error');
    expect($loan->fresh()->status)->toBe(LoanStatus::Analysis)->and($loan->approvals()->count())->toBe(0);
});

it('lists what waits for the person and shows the file only to those who take part', function () {
    $people = committeePeople();
    $loan = analysedFor($people, 20_000_000);
    submitAnalysis($people['analyst'], $loan);

    $this->actingAs($people['kasi'])->get(route('approvals.index'))->assertInertia(fn (Assert $page) => $page
        ->has('loans.data', 1)->where('loans.data.0.my_turn', true)->where('loans.data.0.pending_position', 'Seksi · Kepala Seksi Analis'));
    $this->actingAs($people['kabag'])->get(route('approvals.index'))->assertInertia(fn (Assert $page) => $page->has('loans.data', 0));
    $this->actingAs($people['kasi'])->get(route('approvals.index', ['scope' => 'all']))->assertInertia(fn (Assert $page) => $page->has('loans.data', 1));

    $this->actingAs($people['kasi'])->get(route('approvals.show', $loan))->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('approvals/show')->where('flow.my_turn', true)->where('flow.allowed', ['forward', 'approve', 'reject', 'cancel'])->has('steps', 2));

    $stranger = userWith(['approvals.view'], 'Teller');
    $this->actingAs($stranger)->get(route('approvals.show', $loan))->assertForbidden();

    // Whoever may decide can also read the worksheet.
    $this->actingAs($people['kabag'])->get(route('credit-analysis.show', $loan))->assertOk();
});
