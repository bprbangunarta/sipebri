<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * The credit analysis module is called `credit-analysis` everywhere (URL, route, menu), so its permission is too.
 * The row is renamed in place, so every role that held `analysis.view` keeps it.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->rename('analysis.view', 'credit-analysis.view');
    }

    public function down(): void
    {
        $this->rename('credit-analysis.view', 'analysis.view');
    }

    private function rename(string $from, string $to): void
    {
        if (DB::table('permissions')->where('name', $to)->exists()) {
            return;
        }

        DB::table('permissions')->where('name', $from)->where('is_custom', false)->update(['name' => $to]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
