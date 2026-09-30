<?php

namespace App\Support;

use App\Models\CommitteePath;
use App\Models\CommitteeTier;
use App\Models\User;

/**
 * Resolves the committee path and deciding tier for a product, condition and loan amount.
 * Pure rule lookup (it never touches loan files), so it serves both the authority check screen
 * and the approval workflow.
 */
class Committee
{
    private const STATUS_LABELS = [
        'decider' => 'Decides',
        'escalate' => 'Escalates',
        'blocked' => 'Cannot decide or escalate',
        'not_needed' => 'Not needed',
    ];

    /**
     * @return array<string, mixed>
     */
    public static function resolve(?int $productId, ?string $condition, int $amount): array
    {
        $condition = filled($condition) ? mb_strtoupper(trim($condition)) : null;
        $path = self::path($productId, $condition);

        if (! $path) {
            return [
                'found' => false,
                'message' => $condition === null
                    ? 'There is no committee path for this product under the Normal condition.'
                    : "The condition/category {$condition} is not intended for this product, so it has no committee path.",
                'chain' => [],
                'warnings' => [],
            ];
        }

        $chain = $path->mechanism === 'plafon' ? self::plafonChain($path, $amount) : self::hierarchyChain($path);

        return [
            'found' => true,
            'path' => [
                'id' => $path->id,
                'title' => $path->title(),
                'product_label' => $path->product ? "{$path->product->alias} — {$path->product->name}" : 'All products',
                'condition_label' => $path->condition ?: 'Normal',
                'mechanism' => $path->mechanism,
                'mechanism_label' => CommitteePath::MECHANISMS[$path->mechanism] ?? $path->mechanism,
                'matched_globally' => $path->product_id === null && $productId !== null,
            ],
            'chain' => $chain,
            'decider' => collect($chain)->firstWhere('status', 'decider'),
            'warnings' => self::warnings($path, $chain),
        ];
    }

    /**
     * The condition is looked up as given: the product's own path first, then cross-product paths
     * (e.g. RELOAN). It never falls back to the Normal path, so combinations that are not intended
     * (e.g. KRU + PERLELEAN) are not silently accepted.
     */
    private static function path(?int $productId, ?string $condition): ?CommitteePath
    {
        foreach ([$productId, null] as $candidate) {
            if ($candidate === null && $productId !== null && $condition === null) {
                continue;
            }

            $path = CommitteePath::with(['product', 'tiers'])
                ->where('is_active', true)
                ->where('product_id', $candidate)
                ->where(fn ($q) => $condition === null ? $q->whereNull('condition') : $q->where('condition', $condition))
                ->first();

            if ($path && $path->tiers->isNotEmpty()) {
                return $path;
            }
        }

        return null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function plafonChain(CommitteePath $path, int $amount): array
    {
        $deciderFound = false;

        return $path->tiers->map(function (CommitteeTier $tier) use ($amount, &$deciderFound): array {
            $inRange = $amount >= ($tier->min_amount ?? 0) && ($tier->max_amount === null || $amount <= $tier->max_amount);

            if ($inRange && ! $deciderFound && $tier->canDecide()) {
                $deciderFound = true;
                $status = 'decider';
            } elseif (! $deciderFound && $tier->max_amount !== null && $amount > $tier->max_amount) {
                $status = $tier->can_escalate ? 'escalate' : 'blocked';
            } else {
                $status = 'not_needed';
            }

            return self::row($tier, $status);
        })->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function hierarchyChain(CommitteePath $path): array
    {
        $last = $path->tiers->last(fn (CommitteeTier $t): bool => $t->canDecide());

        return $path->tiers->map(fn (CommitteeTier $tier): array => self::row(
            $tier,
            $last && $tier->is($last) ? 'decider' : ($tier->can_escalate ? 'escalate' : 'blocked'),
        ))->all();
    }

    /**
     * @return array<string, mixed>
     */
    private static function row(CommitteeTier $tier, string $status): array
    {
        return [
            'id' => $tier->id,
            'sort' => $tier->sort,
            'label' => $tier->label,
            'role' => $tier->role,
            'min_amount' => $tier->min_amount,
            'max_amount' => $tier->max_amount,
            'decisions' => array_keys(array_filter([
                'Escalate' => $tier->can_escalate,
                'Approve' => $tier->can_approve,
                'Cancel' => $tier->can_cancel,
                'Reject' => $tier->can_reject,
            ])),
            'status' => $status,
            'status_label' => self::STATUS_LABELS[$status],
            'user_count' => User::role($tier->role)->count(),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $chain
     * @return array<int, string>
     */
    private static function warnings(CommitteePath $path, array $chain): array
    {
        $warnings = [];

        if (! collect($chain)->contains(fn (array $row): bool => $row['status'] === 'decider')) {
            $warnings[] = 'No tier is authorised to decide at this amount. Check the amount range of each tier.';
        }

        foreach ($chain as $row) {
            if ($row['status'] === 'decider' && $row['user_count'] === 0) {
                $warnings[] = "No user holds the role {$row['role']}, so nobody can decide.";
            }
        }

        if ($path->mechanism !== 'plafon') {
            return $warnings;
        }

        $ranges = $path->tiers->filter(fn (CommitteeTier $t): bool => $t->canDecide())->sortBy(fn (CommitteeTier $t): int => $t->min_amount ?? 0)->values();

        foreach ($ranges as $i => $tier) {
            $next = $ranges[$i + 1] ?? null;

            if (! $next || $tier->max_amount === null) {
                continue;
            }

            $nextMin = $next->min_amount ?? 0;

            if ($nextMin > $tier->max_amount + 1) {
                $warnings[] = "There is a gap between {$tier->role} and {$next->role} (".number_format($tier->max_amount + 1, 0, ',', '.').' – '.number_format($nextMin - 1, 0, ',', '.').').';
            }

            if ($nextMin <= $tier->max_amount) {
                $warnings[] = "The amount ranges of {$tier->role} and {$next->role} overlap.";
            }
        }

        return $warnings;
    }
}
