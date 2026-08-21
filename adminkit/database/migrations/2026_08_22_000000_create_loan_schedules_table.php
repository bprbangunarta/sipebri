<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Histori penjadwalan survei. Baris HANYA ditambah — penjadwalan ulang maupun
 * pembatalan membuat baris baru, tidak pernah menimpa atau menghapus yang lama.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->date('survey_date')->nullable();
        });

        Schema::create('loan_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('sequence');
            $table->string('action', 20);
            $table->date('survey_date')->nullable();
            $table->foreignId('surveyor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('surveyor_name')->nullable();
            $table->string('note')->nullable();
            $table->string('reason')->nullable();
            $table->string('created_by');
            $table->timestamp('created_at')->nullable();

            $table->index(['loan_application_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_schedules');

        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropColumn('survey_date');
        });
    }
};
