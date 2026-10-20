<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Facts about a piece of land and its building that do not change from one appraisal to the next; what an appraiser
 * observes on a date lives in the appraisal.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->string('province', 60)->nullable()->after('region_label');
            $table->string('postal_code', 8)->nullable()->after('province');
            $table->string('route_hint', 255)->nullable()->after('postal_code');

            $table->string('right_kind', 8)->nullable()->after('route_hint');
            $table->string('right_holder_name', 100)->nullable()->after('right_kind');
            $table->date('right_issued_at')->nullable()->after('right_holder_name');
            $table->date('right_expires_at')->nullable()->after('right_issued_at');
            $table->string('survey_document', 100)->nullable()->after('right_expires_at');
            $table->date('survey_document_date')->nullable()->after('survey_document');
            $table->unsignedInteger('certificate_land_area')->nullable()->after('survey_document_date');
            $table->string('land_office', 100)->nullable()->after('certificate_land_area');
            $table->string('parcel_number', 50)->nullable()->after('land_office');
            $table->string('acquisition_origin', 150)->nullable()->after('parcel_number');
            $table->string('tax_object_number', 30)->nullable()->after('acquisition_origin');

            $table->string('building_permit_number', 60)->nullable()->after('tax_object_number');
            $table->date('building_permit_date')->nullable()->after('building_permit_number');
            $table->string('operating_certificate_number', 60)->nullable()->after('building_permit_date');
            $table->date('operating_certificate_date')->nullable()->after('operating_certificate_number');
            $table->unsignedInteger('permitted_building_area')->nullable()->after('operating_certificate_date');
            $table->string('permitted_use', 100)->nullable()->after('permitted_building_area');
        });
    }

    public function down(): void
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->dropColumn([
                'province', 'postal_code', 'route_hint', 'right_kind', 'right_holder_name', 'right_issued_at', 'right_expires_at', 'survey_document',
                'survey_document_date', 'certificate_land_area', 'land_office', 'parcel_number', 'acquisition_origin', 'tax_object_number',
                'building_permit_number', 'building_permit_date', 'operating_certificate_number', 'operating_certificate_date',
                'permitted_building_area', 'permitted_use',
            ]);
        });
    }
};
