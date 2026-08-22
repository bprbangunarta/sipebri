<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Jejak pengajuan lembar analisa ke komite kredit. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->timestamp('analysis_submitted_at')->nullable()->after('survey_date');
            $table->string('analysis_submitted_by')->nullable()->after('analysis_submitted_at');
        });
    }

    public function down(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropColumn(['analysis_submitted_at', 'analysis_submitted_by']);
        });
    }
};
