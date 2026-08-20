<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pengajuan kredit (tahap 1 dari 9) sampai tahap persetujuan komite.
 * Kolom analisa disiapkan seminimal mungkin — metode analisa per produk dibuat menyusul.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->id();

            // Identitas berkas
            $table->string('application_code', 12)->unique();
            $table->date('application_date');
            $table->string('status', 20)->default('DIAJUKAN')->index();
            $table->foreignId('office_id')->nullable()->constrained('offices')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('purpose')->nullable();
            $table->string('economic_sector')->nullable();
            $table->string('source', 30)->nullable();

            // Data pemohon (master nasabah belum ada — direkam di berkas)
            $table->string('cif_number', 20)->nullable();
            $table->string('nik', 20)->index();
            $table->string('full_name', 100);
            $table->string('birth_place', 60)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender', 1)->nullable();
            $table->string('marital_status', 20)->nullable();
            $table->string('mother_name', 100)->nullable();
            $table->string('npwp', 25)->nullable();
            $table->string('address')->nullable();
            $table->string('region_code', 8)->nullable();
            $table->string('region_label', 150)->nullable();
            $table->string('phone', 25)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('occupation', 60)->nullable();
            $table->string('employer_name', 100)->nullable();
            $table->unsignedBigInteger('monthly_income')->default(0);
            $table->unsignedBigInteger('other_income')->default(0);
            $table->unsignedBigInteger('monthly_expense')->default(0);
            $table->string('spouse_name', 100)->nullable();
            $table->string('spouse_nik', 20)->nullable();
            $table->unsignedBigInteger('spouse_income')->default(0);

            // Permohonan
            $table->unsignedBigInteger('requested_amount')->default(0);
            $table->unsignedSmallInteger('requested_tenor')->default(0);
            $table->foreignId('method_id')->nullable()->constrained('methods')->nullOnDelete();
            $table->foreignId('installment_id')->nullable()->constrained('installments')->nullOnDelete();
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->decimal('provision_rate', 5, 2)->nullable();
            $table->decimal('admin_rate', 5, 2)->nullable();
            $table->string('collateral_note')->nullable();

            // Analisa (rangka; metode per produk menyusul)
            $table->foreignId('analyst_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('analyzed_at')->nullable();
            $table->text('analysis_note')->nullable();
            $table->decimal('rc_ratio', 8, 2)->nullable();
            $table->unsignedBigInteger('repayment_capacity')->nullable();
            $table->unsignedBigInteger('recommended_amount')->nullable();
            $table->unsignedSmallInteger('recommended_tenor')->nullable();

            // Persetujuan komite
            $table->foreignId('committee_path_id')->nullable()->constrained('committee_paths')->nullOnDelete();
            $table->string('decision', 20)->nullable();
            $table->dateTime('decided_at')->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('decision_note')->nullable();
            $table->unsignedBigInteger('approved_amount')->nullable();
            $table->unsignedSmallInteger('approved_tenor')->nullable();
            $table->decimal('approved_rate', 5, 2)->nullable();

            // Realisasi ke core banking
            $table->string('credit_account', 30)->nullable()->unique();
            $table->date('disbursed_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // Agunan yang dipakai pada satu berkas pengajuan.
        Schema::create('loan_application_collaterals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->constrained('loan_applications')->cascadeOnDelete();
            $table->foreignId('collateral_simulation_id')->constrained('collateral_simulations')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['loan_application_id', 'collateral_simulation_id'], 'loan_collateral_unique');
        });

        // Jejak keputusan berjenjang mengikuti jalur komite produk.
        Schema::create('loan_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->constrained('loan_applications')->cascadeOnDelete();
            $table->foreignId('committee_tier_id')->nullable()->constrained('committee_tiers')->nullOnDelete();
            $table->unsignedSmallInteger('level')->default(1);
            $table->string('role', 60)->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('decision', 20)->nullable();
            $table->string('note')->nullable();
            $table->dateTime('decided_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_approvals');
        Schema::dropIfExists('loan_application_collaterals');
        Schema::dropIfExists('loan_applications');
    }
};
