<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\CommitteePath;
use App\Models\Product;
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

    /** [label, role, min, max, escalate, approve, cancel, reject] */
    private const PLAFON_TIERS = [
        ['Section', RoleName::AnalysisSectionHead->value, 1000, 35_000_000, true, true, true, true],
        ['Committee I', RoleName::AnalysisDepartmentHead->value, 35_000_001, 100_000_000, true, true, true, true],
        ['Committee II', RoleName::BusinessDirector->value, 100_000_001, 300_000_000, true, true, true, true],
        ['Committee III', RoleName::PresidentDirector->value, 300_000_001, null, false, true, true, true],
    ];

    /** [label, role, escalate, approve, cancel, reject] */
    private const HIERARCHY_TIERS = [
        ['Section', RoleName::AnalysisSectionHead->value, true, false, false, false],
        ['Committee I', RoleName::AnalysisDepartmentHead->value, true, false, false, false],
        ['Committee II', RoleName::BusinessDirector->value, true, false, false, false],
        ['Committee III', RoleName::PresidentDirector->value, false, true, true, true],
    ];

    public function run(): void
    {
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

        if ($mechanism === 'plafon') {
            foreach (self::PLAFON_TIERS as $i => [$label, $role, $min, $max, $escalate, $approve, $cancel, $reject]) {
                $path->tiers()->create(['sort' => $i + 1, 'label' => $label, 'role' => $role, 'min_amount' => $min, 'max_amount' => $max, 'can_escalate' => $escalate, 'can_approve' => $approve, 'can_cancel' => $cancel, 'can_reject' => $reject]);
            }

            return;
        }

        foreach (self::HIERARCHY_TIERS as $i => [$label, $role, $escalate, $approve, $cancel, $reject]) {
            $path->tiers()->create(['sort' => $i + 1, 'label' => $label, 'role' => $role, 'can_escalate' => $escalate, 'can_approve' => $approve, 'can_cancel' => $cancel, 'can_reject' => $reject]);
        }
    }
}
