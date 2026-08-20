<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Data pemohon TIDAK disimpan di SIPEBRI — identitas diambil dari API sistem nasabah
 * memakai nomor KTP. Berkas hanya menyimpan nik, full_name, dan cif_number.
 * Sekaligus menambah kolom tahapan pengajuan (permohonan, resort, penugasan, konfirmasi).
 */
return new class extends Migration
{
    private array $personal = [
        'birth_place', 'birth_date', 'gender', 'marital_status', 'mother_name', 'npwp',
        'address', 'region_code', 'region_label', 'phone', 'email', 'occupation',
        'employer_name', 'monthly_income', 'other_income', 'monthly_expense',
        'spouse_name', 'spouse_nik', 'spouse_income',
    ];

    public function up(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropColumn($this->personal);
        });

        Schema::table('loan_applications', function (Blueprint $table) {
            $table->foreignId('institution_id')->nullable()->after('product_id')
                ->constrained('institutions')->nullOnDelete();
            $table->unsignedSmallInteger('tenor_principal')->nullable()->after('requested_tenor');
            $table->unsignedSmallInteger('tenor_interest')->nullable()->after('tenor_principal');
            $table->string('usage_type', 20)->nullable()->after('tenor_interest');
            $table->string('note')->nullable()->after('collateral_note');
            $table->foreignId('supervisor_id')->nullable()->after('analyst_id')
                ->constrained('users')->nullOnDelete();
            $table->foreignId('surveyor_id')->nullable()->after('supervisor_id')
                ->constrained('users')->nullOnDelete();
            $table->dateTime('confirmed_at')->nullable()->after('surveyor_id');
            $table->foreignId('confirmed_by')->nullable()->after('confirmed_at')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('institution_id');
            $table->dropConstrainedForeignId('supervisor_id');
            $table->dropConstrainedForeignId('surveyor_id');
            $table->dropConstrainedForeignId('confirmed_by');
            $table->dropColumn(['tenor_principal', 'tenor_interest', 'usage_type', 'note', 'confirmed_at']);

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
        });
    }
};
