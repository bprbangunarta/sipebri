<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Collateral records follow the core banking (CBS) form; values are stored as entered.
        Schema::create('collaterals', function (Blueprint $table) {
            $table->id();
            $table->string('cbs_id', 50)->nullable()->unique();
            $table->string('credit_account', 30)->nullable()->unique();
            $table->string('collateral_type_code', 8);
            $table->string('binding_type_code', 8)->nullable();
            $table->string('document_number', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('owner_name', 100)->nullable();
            $table->string('owner_address')->nullable();
            $table->string('region_code', 8)->nullable();
            $table->string('region_label', 150)->nullable();

            $table->unsignedBigInteger('guarantee_value')->default(0);
            $table->unsignedBigInteger('adjustment_value')->default(0);
            $table->unsignedBigInteger('fair_value')->default(0);
            $table->unsignedBigInteger('njop_value')->default(0);
            $table->unsignedBigInteger('appraisal_value')->default(0);
            $table->unsignedBigInteger('independent_value')->default(0);
            $table->string('appraiser_name', 100)->nullable();
            $table->date('appraised_at')->nullable();
            $table->string('independent_name', 100)->nullable();
            $table->date('independent_at')->nullable();

            $table->string('condition_code', 8)->nullable();
            $table->date('condition_date')->nullable();
            $table->char('insurance_code', 1)->default('T');
            $table->date('insurance_date')->nullable();
            $table->string('ppap_code', 8)->default('1');

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('collateral_type_code');
        });

        Schema::create('loan_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_code', 12)->unique();
            $table->date('application_date');
            $table->string('status', 20)->default('draft')->index();

            // Applicant: only the national ID, name and CIF number are kept (identity lives in the customer master).
            $table->string('nik', 20)->index();
            $table->string('full_name', 100);
            $table->string('cif_number', 20)->nullable();

            $table->foreignId('office_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('institution_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('committee_path_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('marketing', 100)->nullable();
            $table->string('usage_type', 20)->nullable();

            $table->unsignedBigInteger('requested_amount')->default(0);
            $table->unsignedSmallInteger('requested_tenor')->default(0);
            $table->foreignId('method_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('installment_id')->nullable()->constrained()->restrictOnDelete();
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->string('note')->nullable();

            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['created_by', 'status']);
        });

        Schema::create('loan_application_collaterals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('collateral_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['loan_application_id', 'collateral_id'], 'loan_app_collateral_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_application_collaterals');
        Schema::dropIfExists('loan_applications');
        Schema::dropIfExists('collaterals');
    }
};
