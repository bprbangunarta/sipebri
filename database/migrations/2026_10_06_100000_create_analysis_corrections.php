<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Corrections of the analysis of a file that was already approved. The approval stays as it is; the section head of the
 * file opens the worksheet for a correction (the analyst may ask), the figures the committee decided on are guarded
 * while it is open, and the correction is closed with a note. Every correction is kept.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analysis_corrections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_analysis_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20); // requested, open, closed, declined
            $table->string('reason', 500);
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('requested_at')->nullable();
            $table->foreignId('opened_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('opened_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->string('resolution_note', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analysis_corrections');
    }
};
