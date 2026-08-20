<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * SQLite menyimpan boolean false hasil binding PDO sebagai teks kosong sehingga
 * `where(is_active, 0)` tidak pernah cocok. Normalkan menjadi integer 0/1.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['products', 'committee_paths', 'menus'] as $table) {
            DB::table($table)->whereRaw("typeof(is_active) <> 'integer'")
                ->update(['is_active' => DB::raw("case when is_active in ('1', 'true') then 1 else 0 end")]);
        }
    }

    public function down(): void
    {
        //
    }
};
