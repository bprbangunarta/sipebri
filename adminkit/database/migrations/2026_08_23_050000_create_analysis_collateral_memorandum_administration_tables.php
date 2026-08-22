<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bagian 4 (Analisa Agunan / berita acara pemeriksaan), bagian 7 (Memorandum)
 * dan bagian 8 (Administrasi). Data agunan yang dikirim ke CBS tetap memakai
 * `collateral_simulations`; tabel di sini hanya tambahan hasil pemeriksaan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analysis_collaterals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('collateral_simulation_id')->constrained()->cascadeOnDelete();
            $table->string('kind', 20)->default('LAINNYA');

            // Kendaraan
            $table->string('merek', 100)->nullable();
            $table->string('tipe_kendaraan', 100)->nullable();
            $table->string('tahun', 4)->nullable();
            $table->string('no_rangka', 50)->nullable();
            $table->string('no_mesin', 50)->nullable();
            $table->string('no_polisi', 20)->nullable();
            $table->string('warna', 50)->nullable();

            // Tanah
            $table->unsignedBigInteger('luas')->default(0);

            // Umum
            $table->string('lokasi', 255)->nullable();
            $table->unsignedBigInteger('market_value')->default(0);
            $table->unsignedBigInteger('appraisal_value')->default(0);
            $table->text('catatan')->nullable();

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->unique(['loan_application_id', 'collateral_simulation_id']);
        });

        Schema::create('analysis_memorandums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->unique()->constrained()->cascadeOnDelete();

            // Kebutuhan dana
            foreach (['modal_kerja', 'investasi', 'konsumtif', 'pelunasan_kredit', 'take_over'] as $column) {
                $table->unsignedBigInteger($column)->default(0);
                $table->string("ket_{$column}", 255)->nullable();
            }

            // Usulan
            $table->unsignedBigInteger('usulan_plafond')->default(0);
            $table->unsignedInteger('jangka_waktu')->default(0);
            $table->decimal('b_admin', 6, 2)->default(0);
            $table->decimal('s_bunga', 6, 2)->default(0);
            $table->decimal('b_provisi', 6, 2)->default(0);
            $table->decimal('b_penalti', 6, 2)->default(0);
            $table->string('sebelum_realisasi', 255)->nullable();
            $table->string('syarat_tambahan', 255)->nullable();
            $table->string('pengikatan', 100)->nullable();

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('analysis_administrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->unique()->constrained()->cascadeOnDelete();

            foreach ([
                'administrasi', 'provisi', 'materai',
                'asuransi_jiwa_menurun1', 'asuransi_jiwa_menurun2', 'asuransi_jiwa_menurun3',
                'asuransi_jiwa_tetap1', 'asuransi_jiwa_tetap2', 'asuransi_jiwa',
                'asuransi_kendaraan_motor', 'transaksi_kredit', 'proses_shm',
                'polis_materai', 'pajak_stnk', 'proses_apht', 'by_fiducia',
            ] as $column) {
                $table->unsignedBigInteger($column)->default(0);
            }

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analysis_administrations');
        Schema::dropIfExists('analysis_memorandums');
        Schema::dropIfExists('analysis_collaterals');
    }
};
