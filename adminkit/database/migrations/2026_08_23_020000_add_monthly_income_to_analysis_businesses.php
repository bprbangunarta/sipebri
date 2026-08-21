<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kontribusi usaha PER BULAN ke kemampuan keuangan.
 * Perdagangan/jasa/lainnya = hasil bersih bulanan; pertanian = hasil bersih
 * satu siklus panen dibagi 6 bulan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analysis_businesses', function (Blueprint $table) {
            $table->bigInteger('monthly_income')->default(0)->after('net_profit');
        });
    }

    public function down(): void
    {
        Schema::table('analysis_businesses', function (Blueprint $table) {
            $table->dropColumn('monthly_income');
        });
    }
};
