<?php

namespace Tests\Feature;

use App\Models\CommitteePath;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommitteeRulesTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function admin(): User
    {
        return User::whereHas('roles', fn ($q) => $q->where('name', 'Super Admin'))->firstOrFail();
    }

    public function test_seeder_creates_default_paths(): void
    {
        $this->assertSame(19, CommitteePath::count());
        $this->assertSame('hierarki', CommitteePath::whereNull('product_id')->where('condition', 'RELOAN')->value('mechanism'));
    }

    public function test_duplicate_normal_path_is_rejected(): void
    {
        $product = Product::where('alias', 'KRU')->firstOrFail();

        $this->actingAs($this->admin())
            ->post('/committees', ['product_id' => $product->id, 'mechanism' => 'plafon'])
            ->assertSessionHasErrors('condition');
    }

    public function test_condition_is_uppercased_and_path_created(): void
    {
        $product = Product::where('alias', 'KRISPI')->firstOrFail();

        $this->actingAs($this->admin())
            ->post('/committees', ['product_id' => $product->id, 'condition' => 'perpadian', 'mechanism' => 'plafon'])
            ->assertRedirect();

        $this->assertDatabaseHas('committee_paths', ['product_id' => $product->id, 'condition' => 'PERPADIAN']);
    }

    public function test_tier_routes_are_scoped_to_their_path(): void
    {
        $path = CommitteePath::has('tiers')->firstOrFail();
        $foreignTier = CommitteePath::whereKeyNot($path->id)->has('tiers')->firstOrFail()->tiers()->firstOrFail();

        $this->actingAs($this->admin())
            ->delete("/committees/{$path->id}/tiers/{$foreignTier->id}")
            ->assertNotFound();

        $this->assertModelExists($foreignTier);
    }

    public function test_tier_max_amount_must_be_greater_than_min(): void
    {
        $path = CommitteePath::firstOrFail();

        $this->actingAs($this->admin())
            ->post("/committees/{$path->id}/tiers", [
                'role' => 'Kasi Analis',
                'min_amount' => 50_000_000,
                'max_amount' => 10_000_000,
            ])
            ->assertSessionHasErrors('max_amount');
    }

    public function test_committee_export_returns_xlsx(): void
    {
        $response = $this->actingAs($this->admin())->get('/committees/export');

        $response->assertOk();
        $this->assertStringContainsString('spreadsheetml', (string) $response->headers->get('content-type'));
    }

    public function test_simulation_finds_plafon_decider(): void
    {
        $product = Product::where('alias', 'KRU')->firstOrFail();

        $this->actingAs($this->admin())
            ->getJson("/committees/simulate?product_id={$product->id}&amount=150000000")
            ->assertOk()
            ->assertJsonPath('found', true)
            ->assertJsonPath('path.mechanism', 'plafon')
            ->assertJsonPath('decider.role', 'Direktur Bisnis');
    }

    public function test_simulation_prioritises_condition_over_product_path(): void
    {
        $product = Product::where('alias', 'KRU')->firstOrFail();

        $this->actingAs($this->admin())
            ->getJson("/committees/simulate?product_id={$product->id}&condition=reloan&amount=5000000")
            ->assertOk()
            ->assertJsonPath('path.mechanism', 'hierarki')
            ->assertJsonPath('path.matched_globally', true)
            ->assertJsonPath('decider.role', 'Direktur Utama');
    }

    public function test_simulation_warns_when_amount_has_no_decider(): void
    {
        $product = Product::where('alias', 'KRU')->firstOrFail();

        $response = $this->actingAs($this->admin())
            ->getJson("/committees/simulate?product_id={$product->id}&amount=500")
            ->assertOk()
            ->assertJsonPath('decider', null);

        $this->assertNotEmpty($response->json('warnings'));
    }

    public function test_deleting_path_removes_its_tiers(): void
    {
        $path = CommitteePath::has('tiers')->firstOrFail();
        $tierIds = $path->tiers()->pluck('id');

        $this->actingAs($this->admin())->delete("/committees/{$path->id}")->assertRedirect();

        $this->assertDatabaseMissing('committee_tiers', ['id' => $tierIds->first()]);
    }
}
