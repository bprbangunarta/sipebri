<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->foreignId('surveyor_id')->nullable()->after('supervisor_id')->constrained('users')->nullOnDelete();
            $table->date('survey_date')->nullable()->after('surveyor_id');
        });

        // Append-only history of every schedule, reschedule and cancellation.
        Schema::create('loan_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('sequence');
            $table->string('action', 20);
            $table->date('survey_date')->nullable();
            $table->foreignId('surveyor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('surveyor_name', 100)->nullable();
            $table->string('note')->nullable();
            $table->string('reason')->nullable();
            $table->string('created_by', 100);
            $table->timestamp('created_at')->nullable();

            $table->index(['loan_application_id', 'id']);
        });

        Schema::create('loan_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('sequence')->default(1);
            $table->foreignId('surveyor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('surveyor_name', 100)->nullable();
            $table->date('survey_date')->nullable();
            $table->string('note', 500)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('created_by', 100);
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('loan_survey_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('loan_survey_id')->nullable()->constrained()->nullOnDelete();
            $table->string('path');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('source', 10)->default('camera');
            $table->string('created_by', 100);
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_survey_photos');
        Schema::dropIfExists('loan_surveys');
        Schema::dropIfExists('loan_schedules');
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('surveyor_id');
            $table->dropColumn('survey_date');
        });
    }
};
