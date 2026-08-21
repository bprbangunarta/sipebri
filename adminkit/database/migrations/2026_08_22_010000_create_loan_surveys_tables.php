<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Hasil survei lapangan beserta foto lokasi dan koordinatnya. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('sequence')->default(1);
            $table->foreignId('surveyor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('surveyor_name')->nullable();
            $table->date('survey_date')->nullable();
            $table->string('note', 500)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('created_by');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('loan_survey_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('loan_survey_id')->nullable()->constrained()->nullOnDelete();
            $table->string('path');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('source', 20)->default('KAMERA');
            $table->string('created_by');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_survey_photos');
        Schema::dropIfExists('loan_surveys');
    }
};
