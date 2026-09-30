<?php

use App\Models\CommitteePath;
use App\Models\Product;
use App\Support\Committee;
use Database\Seeders\CommitteeSeeder;
use Database\Seeders\RoleSeeder;
use Inertia\Testing\AssertableInertia as Assert;

function seedCommittees(): void
{
    test()->seed([RoleSeeder::class, CommitteeSeeder::class]);
}

beforeEach(function () {
    $this->actingAs(superAdmin());
    $this->product = fn (string $alias) => Product::firstOrCreate(['alias' => $alias], ['code' => $alias, 'name' => $alias, 'is_active' => true]);
});

it('picks the tier whose amount range covers the loan on an amount path', function () {
    $kru = ($this->product)('KRU');
    seedCommittees();

    $small = Committee::resolve($kru->id, null, 20_000_000);
    expect($small['found'])->toBeTrue()
        ->and($small['decider']['role'])->toBe('Kepala Seksi Analis')
        ->and($small['chain'][1]['status'])->toBe('not_needed');

    $large = Committee::resolve($kru->id, null, 500_000_000);
    expect($large['decider']['role'])->toBe('Direktur Utama')
        ->and(collect($large['chain'])->pluck('status')->all())->toBe(['escalate', 'escalate', 'escalate', 'decider']);
});

it('requires climbing every tier on a hierarchy path', function () {
    $kup = ($this->product)('KUP');
    seedCommittees();

    $result = Committee::resolve($kup->id, null, 1_000_000);

    expect($result['decider']['role'])->toBe('Direktur Utama')
        ->and(collect($result['chain'])->pluck('status')->all())->toBe(['escalate', 'escalate', 'escalate', 'decider']);
});

it('uses cross-product conditions but never falls back to the Normal path', function () {
    $kru = ($this->product)('KRU');
    seedCommittees();

    expect(Committee::resolve($kru->id, 'reloan', 1_000_000)['path']['matched_globally'])->toBeTrue();
    expect(Committee::resolve($kru->id, 'PERLELEAN', 1_000_000)['found'])->toBeFalse();
    expect(Committee::resolve(null, null, 1_000_000)['found'])->toBeFalse();
});

it('warns about gaps, overlaps and roles nobody holds', function () {
    $path = CommitteePath::create(['product_id' => ($this->product)('KRU')->id, 'mechanism' => 'plafon']);
    $this->seed(RoleSeeder::class);
    $path->tiers()->create(['sort' => 1, 'role' => 'Kepala Seksi Analis', 'min_amount' => 0, 'max_amount' => 10_000_000, 'can_approve' => true]);
    $path->tiers()->create(['sort' => 2, 'role' => 'Kepala Bagian Analis', 'min_amount' => 20_000_000, 'max_amount' => null, 'can_approve' => true]);

    $inGap = implode(' ', Committee::resolve($path->product_id, null, 15_000_000)['warnings']);
    expect($inGap)->toContain('No tier is authorised')->toContain('gap between Kepala Seksi Analis and Kepala Bagian Analis');

    $decided = implode(' ', Committee::resolve($path->product_id, null, 5_000_000)['warnings']);
    expect($decided)->toContain('No user holds the role Kepala Seksi Analis');
});

it('serves the authority check as json and validates the amount', function () {
    ($this->product)('KRU');
    seedCommittees();

    $this->getJson(route('committees.authority', ['amount' => 5_000_000]))->assertOk()->assertJsonPath('found', false);
    $this->getJson(route('committees.authority', ['amount' => -1]))->assertUnprocessable();
    $this->getJson(route('committees.authority', ['product_id' => Product::first()->id, 'amount' => 5_000_000]))->assertOk()->assertJsonPath('decider.role', 'Kepala Seksi Analis');
});

it('manages paths and tiers', function () {
    $kru = ($this->product)('KRU');
    $this->seed(RoleSeeder::class);

    $this->post(route('committees.store'), ['product_id' => $kru->id, 'condition' => ' promo ', 'mechanism' => 'plafon', 'is_active' => true])->assertSessionHasNoErrors();
    $path = CommitteePath::firstOrFail();
    expect($path->condition)->toBe('PROMO');

    $this->post(route('committees.store'), ['product_id' => $kru->id, 'condition' => 'promo', 'mechanism' => 'plafon'])->assertSessionHasErrors('condition');
    $this->post(route('committees.store'), ['product_id' => $kru->id, 'mechanism' => 'nope'])->assertSessionHasErrors('mechanism');

    $this->post(route('committees.tiers.store', $path), ['role' => 'Kepala Seksi Analis', 'can_approve' => true, 'min_amount' => 0, 'max_amount' => 5])->assertSessionHasNoErrors();
    $this->post(route('committees.tiers.store', $path), ['role' => 'Kepala Bagian Analis', 'can_approve' => true, 'min_amount' => 6])->assertSessionHasNoErrors();
    $this->post(route('committees.tiers.store', $path), ['role' => 'Nobody'])->assertSessionHasErrors('role');
    $this->post(route('committees.tiers.store', $path), ['role' => 'Kepala Seksi Analis', 'min_amount' => 10, 'max_amount' => 5])->assertSessionHasErrors('max_amount');

    [$first, $second] = $path->tiers()->get()->all();
    $this->put(route('committees.tiers.move', [$path, $second, 'up']))->assertRedirect();
    expect($path->tiers()->pluck('role')->all())->toBe(['Kepala Bagian Analis', 'Kepala Seksi Analis']);

    $this->delete(route('committees.tiers.destroy', [$path, $first]))->assertSessionHas('success');
    expect($path->tiers()->count())->toBe(1);
    $this->delete(route('committees.destroy', $path))->assertRedirect(route('committees.index'));
    expect(CommitteePath::count())->toBe(0);
});

it('does not let a tier be changed through another path', function () {
    $this->seed(RoleSeeder::class);
    $a = CommitteePath::create(['mechanism' => 'hierarki', 'condition' => 'A']);
    $b = CommitteePath::create(['mechanism' => 'hierarki', 'condition' => 'B']);
    $tier = $b->tiers()->create(['sort' => 1, 'role' => 'Kepala Seksi Analis']);

    $this->delete(route('committees.tiers.destroy', [$a, $tier]))->assertNotFound();
});

it('copies tiers from another path and restricts access to Super Admin', function () {
    $kru = ($this->product)('KRU');
    seedCommittees();
    $source = CommitteePath::where('product_id', $kru->id)->firstOrFail();

    $this->post(route('committees.store'), ['condition' => 'copy', 'mechanism' => 'plafon', 'copy_from' => $source->id]);
    expect(CommitteePath::firstWhere('condition', 'COPY')->tiers()->count())->toBe(4);

    $this->get(route('committees.index'))->assertInertia(fn (Assert $page) => $page->component('committees/index'));

    $this->actingAs(userWith(['dashboard.view'], 'Nobody'));
    $this->get(route('committees.index'))->assertForbidden();
    $this->post(route('committees.store'), ['mechanism' => 'plafon'])->assertForbidden();
});
