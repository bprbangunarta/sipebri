<?php

use App\Models\CommitteePath;
use App\Models\Product;
use App\Models\User;
use App\Support\Committee;
use App\Support\CommitteeLevels;
use App\Support\CommitteeMembers;
use Database\Seeders\CommitteeSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->actingAs(superAdmin());
    foreach (['KRU', 'KUP'] as $alias) {
        Product::firstOrCreate(['alias' => $alias], ['code' => $alias, 'name' => $alias, 'is_active' => true]);
    }
    $this->seed([RoleSeeder::class, CommitteeSeeder::class]);
    $this->path = fn (string $alias) => CommitteePath::where('product_id', Product::where('alias', $alias)->value('id'))->whereNull('condition')->firstOrFail();
});

it('seeds the default levels once and puts the seeded paths on them', function () {
    $this->seed(CommitteeSeeder::class);

    expect(CommitteePath::where('is_default', true)->count())->toBe(1)
        ->and(($this->path)('KRU')->follows_default)->toBeTrue()
        ->and(($this->path)('KUP')->follows_default)->toBeTrue()
        ->and(CommitteeLevels::defaultPath()?->title())->toBe('Default authority levels');
});

it('spreads a change of a default limit to every follower and keeps hierarchy paths without amounts', function () {
    $default = CommitteeLevels::defaultPath();
    $first = $default->tiers()->first();

    $this->put("/committees/{$default->id}/tiers/{$first->id}", [
        'label' => $first->label, 'role' => $first->role, 'min_amount' => 1000, 'max_amount' => 50_000_000,
        'can_escalate' => true, 'can_approve' => true, 'can_cancel' => true, 'can_reject' => true,
    ])->assertRedirect();

    $kru = ($this->path)('KRU');
    expect($kru->tiers()->first()->max_amount)->toBe(50_000_000);

    $kup = ($this->path)('KUP')->tiers()->get();
    expect($kup->pluck('max_amount')->filter()->all())->toBe([])
        ->and($kup->last()->can_approve)->toBeTrue()
        ->and($kup->first()->can_approve)->toBeFalse();
});

it('refuses to edit tiers of a path that follows the defaults until it is customized', function () {
    $kru = ($this->path)('KRU');
    $tier = $kru->tiers()->first();

    $this->delete("/committees/{$kru->id}/tiers/{$tier->id}")->assertSessionHas('error');
    expect($kru->tiers()->count())->toBe(5);

    $this->put("/committees/{$kru->id}/follow", ['follow' => false]);
    $this->put("/committees/{$kru->id}/tiers/{$tier->id}", [
        'label' => 'Staff', 'role' => $tier->role, 'is_individual' => true, 'min_amount' => 1000, 'max_amount' => 5_000_000,
        'can_escalate' => true, 'can_approve' => true, 'can_cancel' => true, 'can_reject' => true,
    ])->assertSessionHas('success');

    // A path with limits of its own is not touched by the defaults, and the resolver reads those limits.
    CommitteeLevels::propagate();
    expect($kru->fresh()->tiers()->first()->max_amount)->toBe(5_000_000)
        ->and(Committee::resolve($kru->product_id, null, 8_000_000)['decider'])->toBeNull();

    $this->put("/committees/{$kru->id}/follow", ['follow' => true]);
    expect($kru->fresh()->tiers()->first()->max_amount)->toBe(10_000_000);
});

it('does not list or delete the default path', function () {
    $default = CommitteeLevels::defaultPath();

    $this->get('/committees/paths')->assertInertia(fn ($page) => $page
        ->where('defaultLevels.id', $default->id)
        ->where('paths', fn ($paths) => collect($paths)->doesntContain('is_default', true)));
    $this->delete("/committees/{$default->id}")->assertForbidden();
});

it('lets a new path follow the defaults', function () {
    $this->post('/committees', ['product_id' => null, 'condition' => 'TESTCASE', 'mechanism' => 'plafon', 'is_active' => true, 'follows_default' => true])->assertRedirect();

    $path = CommitteePath::where('condition', 'TESTCASE')->firstOrFail();
    expect($path->follows_default)->toBeTrue()->and($path->tiers()->count())->toBe(5);
});

it('has an individual staff level up to 10 million that is not a committee', function () {
    $kru = ($this->path)('KRU');
    $staff = $kru->tiers()->first();

    expect($staff->role)->toBe('Staff Analis & Appraisal')
        ->and($staff->is_individual)->toBeTrue()
        ->and($staff->max_amount)->toBe(10_000_000)
        ->and($kru->tiers()->skip(1)->first()->min_amount)->toBe(10_000_001);

    $small = Committee::resolve($kru->product_id, null, 8_000_000);
    expect($small['decider']['role'])->toBe('Staff Analis & Appraisal')
        ->and($small['decider']['status_label'])->toBe('Decides (file holder)');
    expect(Committee::resolve($kru->product_id, null, 12_000_000)['decider']['role'])->toBe('Kepala Seksi Analis');

    // Hierarchy paths climb committees only.
    expect(($this->path)('KUP')->tiers()->pluck('role')->all())->not->toContain('Staff Analis & Appraisal');
});

it('leaves the analyst staff out of the committee members but still recognises them as applicants', function () {
    $staff = User::factory()->create()->assignRole('Staff Analis & Appraisal');
    $staff->setNik('9999000000000077', 'manual');

    expect(CommitteeMembers::committee()->whereKey($staff->id)->exists())->toBeFalse()
        ->and(CommitteeMembers::findByNik('9999000000000077')?->is($staff))->toBeTrue();
});

it('shows the default ladder as the hub and the other parts as tabs', function () {
    $this->get('/committees')->assertOk()->assertInertia(fn ($page) => $page
        ->component('committees/index')
        ->has('levels', 5)
        ->where('levels.0.is_individual', true)
        ->has('special'));
    $this->get('/committees/exceptions')->assertInertia(fn ($page) => $page->component('committees/exceptions'));
    $this->get('/committees/paths')->assertInertia(fn ($page) => $page->component('committees/paths'));
});
