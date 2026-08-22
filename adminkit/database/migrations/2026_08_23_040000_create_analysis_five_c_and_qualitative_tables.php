<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Bagian 5 (Analisa 5C) dan bagian 6 (Analisa Kualitatif) — satu baris per berkas. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analysis_five_c', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->unique()->constrained()->cascadeOnDelete();

            // Character (skala 1–3)
            foreach ([
                'gaya_hidup', 'pengendalian_emosi', 'perbuatan_tercela', 'harmonis',
                'konsisten', 'kepatuhan', 'hubungan_sosial',
            ] as $column) {
                $table->unsignedTinyInteger($column)->nullable();
            }

            // Capacity
            foreach ([
                'kontinuitas', 'pengalaman_usaha', 'pertumbuhan_usaha', 'laporan_keuangan',
                'catatan_kredit', 'kondisi_slik', 'aset_diluar_usaha', 'aset_terkait_usaha',
            ] as $column) {
                $table->unsignedTinyInteger($column)->nullable();
            }

            // Capital
            $table->unsignedTinyInteger('sumber_modal')->nullable();

            // Collateral
            foreach ([
                'agunan_utama', 'legalitas_agunan', 'mudah_diuangkan', 'kondisi_kendaraan',
                'aspek_hukum', 'agunan_tambahan', 'legalitas_agunan_tambahan',
                'stabilitas_harga', 'lokasi_shm',
            ] as $column) {
                $table->unsignedTinyInteger($column)->nullable();
            }

            // Condition
            $table->unsignedTinyInteger('kondisi_alam')->nullable();
            $table->unsignedTinyInteger('persaingan_usaha')->nullable();
            $table->unsignedTinyInteger('regulasi_pemerintah')->nullable();

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('analysis_qualitative', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->unique()->constrained()->cascadeOnDelete();

            // Karakter
            $table->unsignedTinyInteger('bi_checking')->nullable();
            $table->unsignedTinyInteger('pihak_berwajib')->nullable();
            $table->string('hubungan_tetangga', 30)->nullable();
            $table->string('pengalaman_tki', 30)->nullable();
            $table->string('ket_pengalaman', 30)->nullable();
            $table->string('pemohon_ada', 150)->nullable();
            $table->string('pendamping_ada', 150)->nullable();
            $table->string('info_masyarakat', 255)->nullable();

            // Kewajiban ke pihak lain (3 baris seperti sistem lama)
            foreach ([1, 2, 3] as $i) {
                $table->string("kewajiban{$i}", 30)->nullable();
                $table->string("ket_kewajiban{$i}", 150)->nullable();
                $table->string("status{$i}", 30)->nullable();
            }

            // Usaha
            $table->string('bahan_baku', 255)->nullable();
            $table->string('proses_olah', 255)->nullable();
            $table->string('target_market', 255)->nullable();
            $table->string('pembayaran', 255)->nullable();
            $table->string('pendukung_usaha', 255)->nullable();
            $table->string('pengurang_usaha', 255)->nullable();
            $table->text('trade_checking')->nullable();

            // SWOT
            $table->string('kekuatan', 255)->nullable();
            $table->string('kelemahan', 255)->nullable();
            $table->string('peluang', 255)->nullable();
            $table->string('ancaman', 255)->nullable();

            // Lainnya
            $table->text('catatan')->nullable();
            $table->text('trade_checking_usaha')->nullable();

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analysis_qualitative');
        Schema::dropIfExists('analysis_five_c');
    }
};
