<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * SQLite menyimpan boolean false hasil binding PDO sebagai teks kosong sehingga
 * `where(is_active, 0)` tidak pernah cocok. Normalkan menjadi integer 0/1.
 * Khusus SQLite — MySQL/MariaDB selalu menyimpan tinyint sehingga tidak perlu diperbaiki.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'sqlite') {
            return;
        }

        foreach (['products', 'committee_paths', 'menus'] as $table) {
            if (! Schema::hasColumn($table, 'is_active')) {
                continue;
            }

            DB::table($table)->whereRaw("typeof(is_active) <> 'integer'")
                ->update(['is_active' => DB::raw("case when is_active in ('1', 'true') then 1 else 0 end")]);
        }
    }

    public function down(): void
    {
        //
    }
};
