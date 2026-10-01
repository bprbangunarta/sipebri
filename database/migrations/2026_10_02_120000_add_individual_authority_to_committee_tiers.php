<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * A tier can be an individual authority instead of a committee: the person who holds the file decides (the analyst staff, up to
 * Rp 10.000.000), so there is no sitting committee and its holders are not committee members.
 *
 * The staff level is added at the bottom of the default amount levels, and the section head level starts above it. Amount paths
 * that follow the defaults get the same change; paths with limits of their own are left as they are.
 */
return new class extends Migration
{
    private const ROLE = 'Staff Analis & Appraisal';

    private const LIMIT = 10_000_000;

    public function up(): void
    {
        Schema::table('committee_tiers', function (Blueprint $table) {
            $table->boolean('is_individual')->default(false)->after('role');
        });

        $paths = DB::table('committee_paths')->where('mechanism', 'plafon')->where(fn ($q) => $q->where('is_default', true)->orWhere('follows_default', true))->pluck('id');

        foreach ($paths as $id) {
            if (DB::table('committee_tiers')->where('committee_path_id', $id)->where('role', self::ROLE)->exists()) {
                continue;
            }

            $first = DB::table('committee_tiers')->where('committee_path_id', $id)->orderBy('sort')->orderBy('id')->first();

            if ($first === null) {
                continue;
            }

            DB::table('committee_tiers')->where('id', $first->id)->update(['min_amount' => self::LIMIT + 1]);
            DB::table('committee_tiers')->where('committee_path_id', $id)->increment('sort');
            DB::table('committee_tiers')->insert([
                'committee_path_id' => $id, 'sort' => 1, 'label' => 'Staff', 'role' => self::ROLE, 'is_individual' => true,
                'min_amount' => $first->min_amount, 'max_amount' => self::LIMIT,
                'can_escalate' => true, 'can_approve' => true, 'can_cancel' => true, 'can_reject' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('committee_tiers')->where('is_individual', true)->delete();
        Schema::table('committee_tiers', fn (Blueprint $table) => $table->dropColumn('is_individual'));
    }
};
