<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Marks permissions created from the permission generator. System permissions come from App\Support\Modules and are
 * kept in step with it by the seeder; custom ones are never removed by the seeder.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->boolean('is_custom')->default(false)->after('guard_name');
        });
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('is_custom');
        });
    }
};
