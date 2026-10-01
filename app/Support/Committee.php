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
        'skipped' => 'Skipped (applicant)',
    ];

    /**
     * Who decides for a product, condition and amount. When the applicant is a committee member, the applicant cannot take part
     * in deciding their own file:
     *  - a tier is skipped when the applicant holds its role and nobody else does (with another holder, such as the second section
     *    head, the tier stays: the other person acts);
     *  - if the tier that would decide is skipped, the next tier above decides; when there is none above (the top committee),
     *    the highest remaining tier below decides, and that is reported as an exception;
     *  - tiers the file would only pass through are simply left out.
     *
     * @return array<string, mixed>
     */
    public static function resolve(?int $productId, ?string $condition, int $amount, ?User $applicant = null): array
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

        $skipped = self::skippedTierIds($path, $applicant);
        [$chain, $exception] = $path->mechanism === 'plafon' ? self::plafonChain($path, $amount, $skipped) : self::hierarchyChain($path, $skipped);

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
            'applicant' => $applicant ? ['id' => $applicant->id, 'name' => $applicant->name, 'role' => $applicant->getRoleNames()->first()] : null,
            'exception' => $exception,
            'warnings' => self::warnings($path, $chain, $applicant !== null),
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
     * Ids of the tiers the applicant takes out of the chain: tiers whose role the applicant holds and nobody else does.
     *
     * @return list<int>
     */
    private static function skippedTierIds(CommitteePath $path, ?User $applicant): array
    {
        if ($applicant === null) {
            return [];
        }

        $ids = $path->tiers
            ->filter(fn (CommitteeTier $t): bool => $applicant->hasRole($t->role) && ! User::role($t->role)->whereKeyNot($applicant->id)->exists())
            ->map(fn (CommitteeTier $t): int => $t->id);

        return array_values($ids->all());
    }

    /**
     * @param  list<int>  $skipped
     * @return array{0: array<int, array<string, mixed>>, 1: string|null} the chain and an exception note, if any
     */
    private static function plafonChain(CommitteePath $path, int $amount, array $skipped): array
    {
        $tiers = $path->tiers;
        $natural = $tiers->first(fn (CommitteeTier $t): bool => $t->canDecide() && $amount >= ($t->min_amount ?? 0) && ($t->max_amount === null || $amount <= $t->max_amount));
        $decider = $natural;
        $exception = null;

        if ($natural !== null && in_array($natural->id, $skipped, true)) {
            $above = $tiers->first(fn (CommitteeTier $t): bool => $t->sort > $natural->sort && $t->canDecide() && ! in_array($t->id, $skipped, true));
            $decider = $above ?? $tiers->last(fn (CommitteeTier $t): bool => $t->sort < $natural->sort && $t->canDecide() && ! in_array($t->id, $skipped, true));

            if ($above === null && $decider !== null) {
                $exception = "{$decider->role} decides although the amount is above its limit: the top committee member is the applicant.";
            }
        }

        $reach = max($natural->sort ?? 0, $decider->sort ?? 0);
        $rows = $tiers->map(function (CommitteeTier $tier) use ($amount, $skipped, $decider, $reach): array {
            if (in_array($tier->id, $skipped, true)) {
                return self::row($tier, $tier->sort <= $reach ? 'skipped' : 'not_needed');
            }

            if ($decider !== null && $tier->is($decider)) {
                return self::row($tier, 'decider');
            }

            $passesThrough = $decider !== null && $tier->sort < $decider->sort && $tier->max_amount !== null && $amount > $tier->max_amount;

            return self::row($tier, $passesThrough ? ($tier->can_escalate ? 'escalate' : 'blocked') : 'not_needed');
        })->all();

        return [$rows, $exception];
    }

    /**
     * @param  list<int>  $skipped
     * @return array{0: array<int, array<string, mixed>>, 1: string|null}
     */
    private static function hierarchyChain(CommitteePath $path, array $skipped): array
    {
        $tiers = $path->tiers;
        $natural = $tiers->last(fn (CommitteeTier $t): bool => $t->canDecide());
        // When the tier that gives the final decision is the applicant's, the highest tier left takes over that decision,
        // whatever its own rights are (on a hierarchy path only the last tier is allowed to decide).
        $last = $natural === null || ! in_array($natural->id, $skipped, true)
            ? $natural
            : $tiers->last(fn (CommitteeTier $t): bool => ! in_array($t->id, $skipped, true));
        $exception = $natural !== null && $last !== null && ! $last->is($natural)
            ? "{$last->role} gives the final decision: the top committee member is the applicant."
            : null;

        $rows = $tiers->map(fn (CommitteeTier $tier): array => self::row(
            $tier,
            in_array($tier->id, $skipped, true) ? 'skipped' : ($last !== null && $tier->is($last) ? 'decider' : ($last !== null && $tier->sort > $last->sort ? 'not_needed' : ($tier->can_escalate ? 'escalate' : 'blocked'))),
        ))->all();

        return [$rows, $exception];
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
            'individual' => $tier->is_individual,
            'status' => $status,
            'status_label' => $tier->is_individual && $status === 'decider' ? 'Decides (file holder)' : self::STATUS_LABELS[$status],
            'user_count' => User::role($tier->role)->count(),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $chain
     * @return array<int, string>
     */
    private static function warnings(CommitteePath $path, array $chain, bool $hasApplicant = false): array
    {
        $warnings = [];

        if (! collect($chain)->contains(fn (array $row): bool => $row['status'] === 'decider')) {
            $warnings[] = $hasApplicant && collect($chain)->contains(fn (array $row): bool => $row['status'] === 'skipped')
                ? 'Nobody is left to decide once the applicant is taken out. Check the committee.'
                : 'No tier is authorised to decide at this amount. Check the amount range of each tier.';
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
