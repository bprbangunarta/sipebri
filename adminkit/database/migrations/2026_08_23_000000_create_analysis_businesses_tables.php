<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Analisa Usaha: satu berkas boleh punya banyak usaha (perdagangan/pertanian/jasa/lainnya). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analysis_businesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20);
            $table->string('code', 20)->unique();
            $table->string('name', 150);
            $table->string('business_length', 20)->nullable();
            $table->string('address', 255)->nullable();

            // Perdagangan
            $table->unsignedBigInteger('daily_purchase')->default(0);
            $table->unsignedBigInteger('cost_of_goods')->default(0);
            $table->unsignedBigInteger('transport_cost')->default(0);
            $table->unsignedBigInteger('employee_cost')->default(0);
            $table->unsignedBigInteger('retribution_cost')->default(0);
            $table->unsignedBigInteger('unload_cost')->default(0);
            $table->unsignedBigInteger('gatel_cost')->default(0);
            $table->unsignedBigInteger('rent_cost')->default(0);

            // Pertanian
            $table->string('economy_sector', 30)->nullable();
            $table->string('plant_type', 50)->nullable();
            $table->unsignedBigInteger('area_own')->default(0);
            $table->unsignedBigInteger('area_rent')->default(0);
            $table->unsignedBigInteger('area_pawn')->default(0);
            $table->decimal('harvest_kw', 12, 2)->default(0);
            $table->unsignedBigInteger('price_per_kw')->default(0);
            $table->unsignedBigInteger('cost_land')->default(0);
            $table->unsignedBigInteger('cost_seed')->default(0);
            $table->unsignedBigInteger('cost_fertilizer')->default(0);
            $table->unsignedBigInteger('cost_pesticide')->default(0);
            $table->unsignedBigInteger('cost_labor')->default(0);
            $table->unsignedBigInteger('cost_irrigation')->default(0);
            $table->unsignedBigInteger('cost_harvest')->default(0);
            $table->unsignedBigInteger('cost_sharecropper')->default(0);
            $table->unsignedBigInteger('cost_tax')->default(0);
            $table->unsignedBigInteger('cost_village')->default(0);
            $table->unsignedBigInteger('cost_amortization')->default(0);
            $table->unsignedBigInteger('cost_other_bank')->default(0);
            $table->unsignedBigInteger('take_portion')->default(0);
            $table->unsignedBigInteger('addition_result')->default(0);
            $table->unsignedBigInteger('other_bank_loan')->default(0);
            $table->unsignedBigInteger('principal_installment')->default(0);

            // Jasa
            $table->unsignedBigInteger('service_income')->default(0);
            $table->unsignedBigInteger('vehicle_tax')->default(0);
            $table->unsignedBigInteger('other_expense')->default(0);

            // Lainnya
            $table->string('business_kind', 50)->nullable();

            // Umum
            $table->unsignedBigInteger('projection_addition')->default(0);
            $table->unsignedBigInteger('revenue')->default(0);
            $table->unsignedBigInteger('expense')->default(0);
            $table->bigInteger('net_profit')->default(0);

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('analysis_business_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analysis_business_id')->constrained()->cascadeOnDelete();
            $table->string('group', 20);
            $table->string('name', 150);
            $table->decimal('qty', 14, 2)->default(0);
            $table->unsignedBigInteger('price')->default(0);
            $table->unsignedBigInteger('sell_price')->default(0);
            $table->unsignedInteger('sort')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analysis_business_items');
        Schema::dropIfExists('analysis_businesses');
    }
};
