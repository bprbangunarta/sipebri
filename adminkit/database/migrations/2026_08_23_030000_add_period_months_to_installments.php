<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kelipatan jangka waktu (bulan) tiap sistem cicilan: dipakai untuk memvalidasi
 * jangka waktu pengajuan dan menghitung setoran pokok pada analisa.
 * 0 = tanpa setoran berkala (NON ANGSURAN) — pokok dibayar sekali di akhir.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('installments', function (Blueprint $table) {
            $table->unsignedTinyInteger('period_months')->default(1)->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('installments', function (Blueprint $table) {
            $table->dropColumn('period_months');
        });
    }
};
