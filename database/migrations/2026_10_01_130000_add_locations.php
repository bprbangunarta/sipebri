<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Positions for the survey. The survey location (where the surveyor did the survey) lives on the file while the survey is being
 * filled in and is copied to the survey record when it is saved; each collateral keeps its own position (the same collateral can
 * be used by several files). Who set a position, when and how (gps, paste, map, photo) is kept next to it.
 * Photos belong to a target (the survey location, or one collateral) and their own GPS data is optional: photos reach the
 * system through chat apps that strip it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('owner_address');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('location_source', 10)->nullable()->after('longitude');
            $table->timestamp('located_at')->nullable()->after('location_source');
            $table->string('located_by', 100)->nullable()->after('located_at');
        });

        Schema::table('loan_applications', function (Blueprint $table) {
            $table->decimal('survey_latitude', 10, 7)->nullable()->after('note');
            $table->decimal('survey_longitude', 10, 7)->nullable()->after('survey_latitude');
            $table->string('survey_source', 10)->nullable()->after('survey_longitude');
            $table->timestamp('survey_located_at')->nullable()->after('survey_source');
            $table->string('survey_located_by', 100)->nullable()->after('survey_located_at');
        });

        Schema::table('loan_survey_photos', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->change();
            $table->decimal('longitude', 10, 7)->nullable()->change();
            $table->timestamp('taken_at')->nullable()->after('longitude');
            // Empty = a photo of the survey location; set = a photo of that collateral.
            $table->foreignId('collateral_id')->nullable()->after('loan_survey_id')->constrained()->nullOnDelete();
        });

        Schema::table('loan_surveys', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->change();
            $table->decimal('longitude', 10, 7)->nullable()->change();
            $table->string('location_source', 10)->nullable()->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('loan_survey_photos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('collateral_id');
            $table->dropColumn('taken_at');
        });
        Schema::table('loan_surveys', fn (Blueprint $table) => $table->dropColumn('location_source'));
        Schema::table('loan_applications', fn (Blueprint $table) => $table->dropColumn(['survey_latitude', 'survey_longitude', 'survey_source', 'survey_located_at', 'survey_located_by']));
        Schema::table('collaterals', fn (Blueprint $table) => $table->dropColumn(['latitude', 'longitude', 'location_source', 'located_at', 'located_by']));
    }
};
