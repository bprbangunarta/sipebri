<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Facts about a motorcycle or a car that do not change from one appraisal to the next (what the BPKB says and what is stamped on the
 * vehicle). What an appraiser observes on a date, such as the odometer, lives in the appraisal.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->string('vehicle_kind', 8)->nullable()->after('permitted_use');
            $table->string('vehicle_brand', 60)->nullable()->after('vehicle_kind');
            $table->string('vehicle_type', 80)->nullable()->after('vehicle_brand');
            $table->string('vehicle_model', 60)->nullable()->after('vehicle_type');
            $table->unsignedSmallInteger('manufacture_year')->nullable()->after('vehicle_model');
            $table->unsignedSmallInteger('registration_year')->nullable()->after('manufacture_year');
            $table->string('vehicle_color', 40)->nullable()->after('registration_year');
            $table->string('plate_number', 20)->nullable()->after('vehicle_color');
            $table->string('chassis_number', 40)->nullable()->after('plate_number')->index();
            $table->string('engine_number', 40)->nullable()->after('chassis_number')->index();
            $table->unsignedSmallInteger('engine_capacity')->nullable()->after('engine_number');
            $table->string('fuel', 30)->nullable()->after('engine_capacity');
            $table->string('transmission', 10)->nullable()->after('fuel');
            $table->string('drive', 30)->nullable()->after('transmission');
            $table->string('unit_number', 40)->nullable()->after('drive');
            $table->string('bpkb_number', 40)->nullable()->after('unit_number');
            $table->date('bpkb_date')->nullable()->after('bpkb_number');
            $table->string('bpkb_name', 100)->nullable()->after('bpkb_date');
            $table->string('bpkb_address', 255)->nullable()->after('bpkb_name');
            $table->string('stnk_number', 40)->nullable()->after('bpkb_address');
            $table->string('stnk_name', 100)->nullable()->after('stnk_number');
        });

        Schema::table('collateral_appraisals', function (Blueprint $table) {
            $table->string('variant', 10)->nullable()->after('template_version');
        });
    }

    public function down(): void
    {
        Schema::table('collateral_appraisals', function (Blueprint $table) {
            $table->dropColumn('variant');
        });

        Schema::table('collaterals', function (Blueprint $table) {
            $table->dropIndex(['chassis_number']);
            $table->dropIndex(['engine_number']);
            $table->dropColumn([
                'vehicle_kind', 'vehicle_brand', 'vehicle_type', 'vehicle_model', 'manufacture_year', 'registration_year', 'vehicle_color', 'plate_number',
                'chassis_number', 'engine_number', 'engine_capacity', 'fuel', 'transmission', 'drive', 'unit_number', 'bpkb_number', 'bpkb_date', 'bpkb_name',
                'bpkb_address', 'stnk_number', 'stnk_name',
            ]);
        });
    }
};
