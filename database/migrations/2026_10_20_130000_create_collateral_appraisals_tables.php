<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * An appraisal is a dated report on one collateral. A collateral is appraised again for a new loan, a top-up, or a review,
 * so appraisals pile up as history: a finalised one is never changed. What an appraiser filled in lives as entries
 * (section, key, value) defined by the template the appraisal follows; the figures that lists and the credit analysis read
 * are columns of the appraisal.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collateral_appraisals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collateral_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('sequence');
            $table->string('template', 30);
            $table->unsignedSmallInteger('template_version');
            $table->string('status', 10)->default('draft');
            $table->string('purpose', 10)->default('new');
            $table->string('inspection', 10)->nullable();

            $table->foreignId('loan_application_id')->nullable()->constrained()->nullOnDelete();
            $table->string('debtor_name', 100)->nullable();
            $table->string('debtor_cif', 30)->nullable();
            $table->string('product_label', 100)->nullable();
            $table->unsignedBigInteger('credit_ceiling')->default(0);

            $table->string('owner_address', 255)->nullable();
            $table->string('owner_relation', 100)->nullable();
            $table->string('owner_contact', 50)->nullable();

            $table->string('appraiser_name', 100)->nullable();
            $table->string('reviewer_name', 100)->nullable();
            $table->date('effective_date')->nullable();
            $table->date('report_date')->nullable();
            $table->date('valid_until')->nullable();

            $table->decimal('haircut_percent', 5, 2)->nullable();
            $table->unsignedBigInteger('market_value')->default(0);
            $table->unsignedBigInteger('liquidation_value')->default(0);
            $table->unsignedBigInteger('forced_sale_value')->nullable();
            $table->unsignedBigInteger('rounded_value')->nullable();
            $table->unsignedBigInteger('credit_basis_value')->nullable();

            $table->string('decision', 12)->nullable();
            $table->text('main_conditions')->nullable();
            $table->string('reappraisal_frequency', 100)->nullable();

            $table->timestamp('finalized_at')->nullable();
            $table->string('finalized_by', 100)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['collateral_id', 'sequence']);
            $table->index(['collateral_id', 'status']);
        });

        Schema::create('collateral_appraisal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collateral_appraisal_id')->constrained()->cascadeOnDelete();
            $table->string('section', 30);
            $table->string('item', 60);
            $table->text('value')->nullable();
            $table->string('note', 500)->nullable();
            $table->timestamps();

            $table->unique(['collateral_appraisal_id', 'item'], 'appraisal_entries_item_unique');
        });

        Schema::create('collateral_appraisal_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collateral_appraisal_id')->constrained()->cascadeOnDelete();
            $table->string('item', 60);
            $table->string('path');
            $table->string('created_by', 100);
            $table->timestamp('created_at')->nullable();

            $table->index(['collateral_appraisal_id', 'item'], 'appraisal_photos_item_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collateral_appraisal_photos');
        Schema::dropIfExists('collateral_appraisal_entries');
        Schema::dropIfExists('collateral_appraisals');
    }
};
