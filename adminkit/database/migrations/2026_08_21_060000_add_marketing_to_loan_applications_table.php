<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Nama marketing/petugas pemasar pada berkas pengajuan (opsional). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->string('marketing', 100)->nullable()->after('institution_id');
        });
    }

    public function down(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropColumn('marketing');
        });
    }
};
