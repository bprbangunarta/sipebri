<?php

use App\Models\CommitteePath;
use App\Models\CommitteeTier;
use App\Models\Product;
use App\Models\User;
use App\Support\Committee;
use Database\Seeders\RoleSeeder;

/**
 * The amount path of the bank (Kasi up to 35 m, Kabag up to 100 m, Dirbis up to 300 m, Dirut above), a hierarchy path, and the people
 * holding the roles: two section heads, one of everything else.
 *
 * @return array<string, mixed>
 */
function conflictSetup(): array
{
    test()->seed(RoleSeeder::class);

    $kru = Product::create(['code' => '01', 'alias' => 'KRU', 'name' => 'GENERAL', 'is_active' => true]);
    $kup = Product::create(['code' => '02', 'alias' => 'KUP', 'name' => 'EMPLOYEE', 'is_active' => true]);
    $tiers = [
        ['Kepala Seksi Analis', 0, 35_000_000], ['Kepala Bagian Analis', 35_000_001, 100_000_000],
        ['Direktur Bisnis', 100_000_001, 300_000_000], ['Direktur Utama', 300_000_001, null],
    ];

    foreach (['plafon' => $kru, 'hierarki' => $kup] as $mechanism => $product) {
        $path = CommitteePath::create(['product_id' => $product->id, 'mechanism' => $mechanism]);

        foreach ($tiers as $i => [$role, $min, $max]) {
            $last = $i === count($tiers) - 1;
            $mechanism === 'plafon'
                ? $path->tiers()->create(['sort' => $i + 1, 'label' => $role, 'role' => $role, 'min_amount' => $min, 'max_amount' => $max, 'can_escalate' => ! $last, 'can_approve' => true])
                : $path->tiers()->create(['sort' => $i + 1, 'label' => $role, 'role' => $role, 'can_escalate' => ! $last, 'can_approve' => $last]);
        }
    }

    return [
        'kru' => $kru, 'kup' => $kup,
        'kasiA' => User::factory()->create(['name' => 'Kasi A'])->assignRole('Kepala Seksi Analis'),
        'kasiB' => User::factory()->create(['name' => 'Kasi B'])->assignRole('Kepala Seksi Analis'),
        'kabag' => User::factory()->create(['name' => 'Kabag'])->assignRole('Kepala Bagian Analis'),
        'dirbis' => User::factory()->create(['name' => 'Dirbis'])->assignRole('Direktur Bisnis'),
        'dirut' => User::factory()->create(['name' => 'Dirut'])->assignRole('Direktur Utama'),
    ];
}

/**
 * @return array{decider: string|null, statuses: list<string>, exception: string|null}
 */
function conflictOutcome(Product $product, int $amount, ?User $applicant): array
{
    $result = Committee::resolve($product->id, null, $amount, $applicant);

    return [
        'decider' => $result['decider']['role'] ?? null,
        'statuses' => collect($result['chain'])->pluck('status')->all(),
        'exception' => $result['exception'],
    ];
}

it('changes nothing when the applicant is not a committee member', function () {
    $s = conflictSetup();

    expect(conflictOutcome($s['kru'], 20_000_000, null))->toBe(['decider' => 'Kepala Seksi Analis', 'statuses' => ['decider', 'not_needed', 'not_needed', 'not_needed'], 'exception' => null])
        ->and(conflictOutcome($s['kru'], 500_000_000, null)['decider'])->toBe('Direktur Utama')
        ->and(conflictOutcome($s['kru'], 20_000_000, User::factory()->create()->assignRole('Teller'))['statuses'])->toBe(['decider', 'not_needed', 'not_needed', 'not_needed']);
});

it('keeps the section head tier when another section head can act for the applicant', function () {
    $s = conflictSetup();

    // Kasi A applies; Kasi B holds the same role, so the tier stands and decides within its limit.
    expect(conflictOutcome($s['kru'], 20_000_000, $s['kasiA']))->toBe(['decider' => 'Kepala Seksi Analis', 'statuses' => ['decider', 'not_needed', 'not_needed', 'not_needed'], 'exception' => null]);
});

it('moves the decision up when the tier that would decide is the applicant alone', function () {
    $s = conflictSetup();
    $s['kasiB']->removeRole('Kepala Seksi Analis'); // only Kasi A is left

    expect(conflictOutcome($s['kru'], 20_000_000, $s['kasiA']))
        ->toBe(['decider' => 'Kepala Bagian Analis', 'statuses' => ['skipped', 'decider', 'not_needed', 'not_needed'], 'exception' => null]);
});

it('jumps over the applicant when the file would only pass through their tier', function () {
    $s = conflictSetup();

    expect(conflictOutcome($s['kru'], 200_000_000, $s['kabag']))
        ->toBe(['decider' => 'Direktur Bisnis', 'statuses' => ['escalate', 'skipped', 'decider', 'not_needed'], 'exception' => null]);
});

it('lets the next committee decide when the applicant is in the tier that would decide', function () {
    $s = conflictSetup();

    expect(conflictOutcome($s['kru'], 150_000_000, $s['dirbis']))
        ->toBe(['decider' => 'Direktur Utama', 'statuses' => ['escalate', 'escalate', 'skipped', 'decider'], 'exception' => null]);
});

it('hands the final decision to the committee below when the president director is the applicant', function () {
    $s = conflictSetup();
    $result = Committee::resolve($s['kru']->id, null, 500_000_000, $s['dirut']);

    expect($result['decider']['role'])->toBe('Direktur Bisnis')
        ->and(collect($result['chain'])->pluck('status')->all())->toBe(['escalate', 'escalate', 'decider', 'skipped'])
        ->and($result['exception'])->toContain('Direktur Bisnis memutus meski plafon melebihi batasnya')
        ->and($result['applicant'])->toMatchArray(['name' => 'Dirut']);

    // A small file never reaches the top committee, so the applicant being there changes nothing.
    expect(conflictOutcome($s['kru'], 20_000_000, $s['dirut']))->toBe(['decider' => 'Kepala Seksi Analis', 'statuses' => ['decider', 'not_needed', 'not_needed', 'not_needed'], 'exception' => null]);
});

it('leaves out the applicant on a hierarchy path and ends at the last remaining committee', function () {
    $s = conflictSetup();

    expect(conflictOutcome($s['kup'], 1_000_000, null)['decider'])->toBe('Direktur Utama')
        ->and(conflictOutcome($s['kup'], 1_000_000, $s['kabag']))->toBe(['decider' => 'Direktur Utama', 'statuses' => ['escalate', 'skipped', 'escalate', 'decider'], 'exception' => null])
        ->and(conflictOutcome($s['kup'], 1_000_000, $s['kasiA'])['statuses'])->toBe(['escalate', 'escalate', 'escalate', 'decider']); // Kasi B acts

    $top = conflictOutcome($s['kup'], 1_000_000, $s['dirut']);
    expect($top['decider'])->toBe('Direktur Bisnis')->and($top['statuses'])->toBe(['escalate', 'escalate', 'decider', 'skipped'])->and($top['exception'])->toContain('keputusan akhir');
});

it('says so when nobody is left to decide', function () {
    $s = conflictSetup();
    CommitteeTier::query()->whereIn('role', ['Kepala Bagian Analis', 'Direktur Bisnis', 'Direktur Utama'])->delete();
    $s['kasiB']->removeRole('Kepala Seksi Analis');

    $result = Committee::resolve($s['kru']->id, null, 20_000_000, $s['kasiA']);

    expect($result['decider'])->toBeNull()->and(implode(' ', $result['warnings']))->toContain('Tidak ada yang tersisa untuk memutus');
});

it('simulates a committee member applicant on the authority check', function () {
    $s = conflictSetup();
    $this->actingAs(superAdmin());

    $this->getJson(route('committees.authority', ['product_id' => $s['kru']->id, 'amount' => 500_000_000, 'applicant_id' => $s['dirut']->id]))->assertOk()
        ->assertJsonPath('decider.role', 'Direktur Bisnis')->assertJsonPath('applicant.name', 'Dirut')->assertJsonPath('chain.3.status', 'skipped');
    $this->getJson(route('committees.authority', ['product_id' => $s['kru']->id, 'amount' => 500_000_000]))->assertOk()
        ->assertJsonPath('decider.role', 'Direktur Utama')->assertJsonPath('applicant', null);
    $this->getJson(route('committees.authority', ['product_id' => $s['kru']->id, 'amount' => 1, 'applicant_id' => User::factory()->create()->id]))->assertJsonValidationErrors('applicant_id');
});
