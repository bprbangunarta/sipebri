<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Simpan acuan baris wilayah agar pilihan bertahap bisa dipulihkan saat menyunting. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('collateral_simulations', function (Blueprint $table) {
            $table->unsignedBigInteger('region_id')->nullable()->after('region_code');
        });
    }

    public function down(): void
    {
        Schema::table('collateral_simulations', function (Blueprint $table) {
            $table->dropColumn('region_id');
        });
    }
};
