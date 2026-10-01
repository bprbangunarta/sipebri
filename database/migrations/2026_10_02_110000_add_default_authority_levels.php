<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The authority limits of the committees are the same on almost every path, so they live in one place: a "default" path whose
 * tiers every path that follows the defaults copies. Changing a limit there reaches all of them at once. A path can still
 * leave the defaults (follows_default = false) and keep limits of its own.
 *
 * Existing data: the first amount path becomes the default, and every path that already matches it follows it.
 */
return new class extends Migration
{
    private const TIER_COLUMNS = ['sort', 'label', 'role', 'min_amount', 'max_amount', 'can_escalate', 'can_approve', 'can_cancel', 'can_reject'];

    public function up(): void
    {
        Schema::table('committee_paths', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('is_active');
            $table->boolean('follows_default')->default(false)->after('is_default');
        });

        $source = DB::table('committee_paths')->where('mechanism', 'plafon')->whereExists(fn ($q) => $q->selectRaw('1')->from('committee_tiers')->whereColumn('committee_tiers.committee_path_id', 'committee_paths.id'))->orderBy('id')->first();

        if ($source === null) {
            return;
        }

        $levels = DB::table('committee_tiers')->where('committee_path_id', $source->id)->orderBy('sort')->orderBy('id')->get(self::TIER_COLUMNS);
        $now = now();
        $defaultId = DB::table('committee_paths')->insertGetId([
            'product_id' => null, 'condition' => '(DEFAULT)', 'mechanism' => 'plafon', 'is_active' => false, 'is_default' => true, 'follows_default' => false,
            'note' => 'Authority levels shared by every path that follows the defaults.', 'created_at' => $now, 'updated_at' => $now,
        ]);

        DB::table('committee_tiers')->insert($levels->map(fn ($l): array => [...(array) $l, 'committee_path_id' => $defaultId, 'created_at' => $now, 'updated_at' => $now])->all());

        foreach (DB::table('committee_paths')->where('is_default', false)->get() as $path) {
            $tiers = DB::table('committee_tiers')->where('committee_path_id', $path->id)->orderBy('sort')->orderBy('id')->get(self::TIER_COLUMNS);

            // A path follows the defaults when it says the same: same roles in the same order, and for an amount path the same limits.
            $same = $tiers->count() === $levels->count() && $tiers->every(function ($tier, int $i) use ($levels, $path): bool {
                $level = $levels[$i];

                return $tier->role === $level->role && ($path->mechanism !== 'plafon' || ($tier->max_amount === $level->max_amount && (bool) $tier->can_escalate === (bool) $level->can_escalate));
            });

            DB::table('committee_paths')->where('id', $path->id)->update(['follows_default' => $same]);
        }
    }

    public function down(): void
    {
        $default = DB::table('committee_paths')->where('is_default', true)->value('id');

        if ($default !== null) {
            DB::table('committee_tiers')->where('committee_path_id', $default)->delete();
            DB::table('committee_paths')->where('id', $default)->delete();
        }

        Schema::table('committee_paths', fn (Blueprint $table) => $table->dropColumn(['is_default', 'follows_default']));
    }
};
