<?php

namespace App\Support;

use App\Audit\Audit;
use App\Models\CommitteePath;
use App\Models\CommitteeTier;
use Illuminate\Support\Facades\DB;

/**
 * The default authority levels: one list of committee tiers (role, amount limit, decisions) that every path following the
 * defaults copies. Editing the default path's tiers calls `propagate()`, which rewrites the tiers of all followers; a path
 * that leaves the defaults keeps its own tiers.
 *
 * On an amount path a follower copies the limits as they are. On a hierarchy path only the order of the roles matters: every tier
 * escalates and only the last decides, so a follower derives that from the same list.
 */
class CommitteeLevels
{
    public static function defaultPath(): ?CommitteePath
    {
        return CommitteePath::query()->where('is_default', true)->first();
    }

    /** Make the path's tiers a copy of the defaults (replacing what it had). */
    public static function attach(CommitteePath $path): void
    {
        $default = self::defaultPath();

        if ($default === null || $path->is($default)) {
            return;
        }

        $hierarchy = $path->mechanism !== 'plafon';
        // A hierarchy path climbs committees only; an individual authority (the file holder) has no seat in it.
        $levels = $default->tiers()->get()->reject(fn (CommitteeTier $level): bool => $hierarchy && $level->is_individual)->values();

        DB::transaction(function () use ($path, $levels, $hierarchy): void {
            $path->tiers()->get()->each(fn (CommitteeTier $tier) => $tier->delete());

            foreach ($levels->values() as $i => $level) {
                $last = $i === $levels->count() - 1;

                $path->tiers()->create([
                    'sort' => $level->sort, 'label' => $level->label, 'role' => $level->role, 'is_individual' => $level->is_individual,
                    'min_amount' => $hierarchy ? null : $level->min_amount,
                    'max_amount' => $hierarchy ? null : $level->max_amount,
                    'can_escalate' => $hierarchy ? ! $last : $level->can_escalate,
                    'can_approve' => $hierarchy ? $last : $level->can_approve,
                    'can_cancel' => $hierarchy ? $last && $level->can_cancel : $level->can_cancel,
                    'can_reject' => $hierarchy ? $last && $level->can_reject : $level->can_reject,
                ]);
            }
        });
    }

    /**
     * Apply the defaults to every path that follows them.
     *
     * @return int the number of paths rewritten
     */
    public static function propagate(): int
    {
        $paths = CommitteePath::query()->where('follows_default', true)->where('is_default', false)->get();
        $paths->each(fn (CommitteePath $path) => self::attach($path));

        Audit::record('committees.levels_propagated', 'committees', 'levels_propagated', self::defaultPath(), context: ['paths' => $paths->count()], label: 'Jenjang wewenang bawaan');

        return $paths->count();
    }

    public static function followers(): int
    {
        return CommitteePath::query()->where('follows_default', true)->where('is_default', false)->count();
    }
}
