<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\CommitteePath;
use App\Models\Product;
use App\Support\CommitteeLevels;
use Illuminate\Database\Seeder;

/**
 * Default committee rules:
 *  - "plafon" paths (authority by amount) for the general products and KBT PERPADIAN;
 *  - "hierarki" paths (must climb every tier, only the last decides) for KUP, KKO, KBT PERLELEAN
 *    and the cross-product RELOAN category.
 * Tiers hold decision levels only. Idempotent; tiers are added only to paths that have none,
 * so manual adjustments are never overwritten.
 */
class CommitteeSeeder extends Seeder
{
    private const PLAFON_PRODUCTS = ['KRU', 'KRM', 'PRK', 'KTO', 'KPS', 'KIH', 'KPJ', 'KRS', 'KPN', 'KIU', 'KTA', 'KPMI', 'KPP', 'KRISPI'];

    private const HIERARCHY_PRODUCTS = ['KUP', 'KKO'];

    /** [label, role, min, max, escalate, approve, cancel, reject, individual] */
    private const PLAFON_TIERS = [
        ['Staf', RoleName::Analyst->value, 1000, 10_000_000, true, true, true, true, true],
        ['Seksi', RoleName::AnalysisSectionHead->value, 10_000_001, 35_000_000, true, true, true, true, false],
        ['Komite I', RoleName::AnalysisDepartmentHead->value, 35_000_001, 100_000_000, true, true, true, true, false],
        ['Komite II', RoleName::BusinessDirector->value, 100_000_001, 300_000_000, true, true, true, true, false],
        ['Komite III', RoleName::PresidentDirector->value, 300_000_001, null, false, true, true, true, false],
    ];

    public function run(): void
    {
        $this->defaultLevels();

        foreach (self::PLAFON_PRODUCTS as $alias) {
            $this->path($alias, null, 'plafon', 'Authority by amount (general path).');
        }

        $this->path('KBT', 'PERPADIAN', 'plafon', 'Actual crop-farming loans.');

        foreach (self::HIERARCHY_PRODUCTS as $alias) {
            $this->path($alias, null, 'hierarki', 'Loans for employees.');
        }

        $this->path('KBT', 'PERLELEAN', 'hierarki', 'Customers in partnership with a company.');
        $this->path(null, 'RELOAN', 'hierarki', 'Applies to every product and overrides the amount-limit path.');
    }

    /** The default authority levels, created once; later edits by an admin are never overwritten. */
    private function defaultLevels(): void
    {
        if (CommitteeLevels::defaultPath() !== null) {
            return;
        }

        $path = CommitteePath::create(['product_id' => null, 'condition' => '(DEFAULT)', 'mechanism' => 'plafon', 'is_active' => false, 'is_default' => true, 'note' => 'Authority levels shared by every path that follows the defaults.']);

        foreach (self::PLAFON_TIERS as $i => [$label, $role, $min, $max, $escalate, $approve, $cancel, $reject, $individual]) {
            $path->tiers()->create(['sort' => $i + 1, 'label' => $label, 'role' => $role, 'is_individual' => $individual, 'min_amount' => $min, 'max_amount' => $max, 'can_escalate' => $escalate, 'can_approve' => $approve, 'can_cancel' => $cancel, 'can_reject' => $reject]);
        }
    }

    private function path(?string $alias, ?string $condition, string $mechanism, string $note): void
    {
        $productId = $alias ? Product::where('alias', $alias)->value('id') : null;

        if ($alias && ! $productId) {
            return;
        }

        $path = CommitteePath::firstOrNew(['product_id' => $productId, 'condition' => $condition]);
        $path->fill(['mechanism' => $mechanism, 'note' => $note, 'is_active' => true])->save();

        if ($path->tiers()->exists()) {
            return;
        }

        // A path created here starts on the shared default levels (adjust them once, every follower changes).
        $path->forceFill(['follows_default' => true])->save();
        CommitteeLevels::attach($path);
    }
}
