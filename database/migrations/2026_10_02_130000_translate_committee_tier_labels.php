<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Tier names are shown in the UI, which is in Indonesian. Only the names the seeder gave are renamed; a name an admin typed is left alone.
 */
return new class extends Migration
{
    private const NAMES = ['Staff' => 'Staf', 'Section' => 'Seksi', 'Committee I' => 'Komite I', 'Committee II' => 'Komite II', 'Committee III' => 'Komite III'];

    public function up(): void
    {
        foreach (self::NAMES as $from => $to) {
            DB::table('committee_tiers')->where('label', $from)->update(['label' => $to]);
        }
    }

    public function down(): void
    {
        foreach (self::NAMES as $from => $to) {
            DB::table('committee_tiers')->where('label', $to)->update(['label' => $from]);
        }
    }
};
