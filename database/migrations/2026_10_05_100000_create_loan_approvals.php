<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Committee decisions. When the analysis goes to the committee the steps of the route (the tiers that have to act) are
 * written as pending rows; each step is filled in by the person who decides it. The outcome is kept on the file.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('committee_tier_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('sort');
            $table->string('tier_label', 50)->nullable();
            $table->string('role', 100);
            $table->boolean('is_individual')->default(false);
            $table->boolean('waived')->default(false); // decides although it is outside the tier's own limit (applicant exception)
            $table->string('decision', 20)->nullable(); // forward, approve, reject, cancel; empty while pending
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('decided_by_name', 150)->nullable();
            $table->foreignId('method_id')->nullable()->constrained('methods')->nullOnDelete();
            $table->unsignedBigInteger('amount')->default(0);
            $table->unsignedInteger('tenor')->default(0);
            $table->decimal('interest_rate', 6, 2)->default(0);
            $table->decimal('provision_rate', 6, 2)->default(0);
            $table->decimal('admin_rate', 6, 2)->default(0);
            $table->unsignedBigInteger('max_amount')->default(0);
            $table->decimal('rc_ratio', 8, 2)->default(0);
            $table->string('note', 255)->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();

            $table->unique(['loan_application_id', 'sort']);
        });

        Schema::table('loan_applications', function (Blueprint $table) {
            $table->unsignedBigInteger('approved_amount')->default(0);
            $table->unsignedInteger('approved_tenor')->default(0);
            $table->decimal('approved_rate', 6, 2)->default(0);
            $table->decimal('rc_ratio', 8, 2)->default(0);
            $table->timestamp('decided_at')->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('decision_note', 255)->nullable();
            $table->string('committee_exception', 500)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('decided_by');
            $table->dropColumn(['approved_amount', 'approved_tenor', 'approved_rate', 'rc_ratio', 'decided_at', 'decision_note', 'committee_exception']);
        });

        Schema::dropIfExists('loan_approvals');
    }
};
