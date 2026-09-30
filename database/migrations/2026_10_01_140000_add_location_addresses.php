<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The approximate address that reverse geocoding gave for a saved position, kept as a copy taken when the position was
 * set (so it does not depend on the geocoding service later). It is a hint for people, never a source of truth.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('collaterals', fn (Blueprint $table) => $table->text('location_address')->nullable()->after('located_by'));
        Schema::table('loan_applications', fn (Blueprint $table) => $table->text('survey_address')->nullable()->after('survey_located_by'));
        Schema::table('loan_surveys', fn (Blueprint $table) => $table->text('location_address')->nullable()->after('location_source'));
    }

    public function down(): void
    {
        Schema::table('loan_surveys', fn (Blueprint $table) => $table->dropColumn('location_address'));
        Schema::table('loan_applications', fn (Blueprint $table) => $table->dropColumn('survey_address'));
        Schema::table('collaterals', fn (Blueprint $table) => $table->dropColumn('location_address'));
    }
};
