<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Lembar analisa per berkas: analisa keuangan (rumah tangga) & analisa kepemilikan. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analysis_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->unique()->constrained()->cascadeOnDelete();

            // Analisa keuangan — biaya rumah tangga per bulan
            $table->unsignedBigInteger('cost_staple')->default(0);
            $table->unsignedBigInteger('cost_education')->default(0);
            $table->unsignedBigInteger('cost_children')->default(0);
            $table->unsignedBigInteger('cost_cigarette')->default(0);
            $table->unsignedBigInteger('cost_health')->default(0);
            $table->unsignedBigInteger('cost_gatel')->default(0);
            $table->unsignedBigInteger('cost_social')->default(0);

            // Analisa kepemilikan — harta
            $table->string('asset_house', 20)->nullable();
            $table->string('asset_car', 20)->nullable();
            $table->string('asset_motorcycle', 20)->nullable();
            $table->string('asset_computer', 20)->nullable();
            $table->string('asset_washer', 20)->nullable();
            $table->string('asset_tv', 20)->nullable();
            $table->string('asset_chair', 20)->nullable();
            $table->string('asset_cabinet', 20)->nullable();

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('analysis_sheet_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analysis_sheet_id')->constrained()->cascadeOnDelete();
            $table->string('group', 20);
            $table->string('name', 150);
            $table->unsignedBigInteger('amount')->default(0);
            $table->unsignedInteger('sort')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analysis_sheet_items');
        Schema::dropIfExists('analysis_sheets');
    }
};
