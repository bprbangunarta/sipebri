<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Contoh/simulasi data agunan mengikuti form CBS.
 * SIPEBRI hanya merekam; seluruh perhitungan (PPKA/PPAP) dilakukan CBS.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collateral_simulations', function (Blueprint $table) {
            $table->id();
            $table->string('collateral_id')->nullable();     // Agunan ID di CBS
            $table->unsignedTinyInteger('paripasu')->default(0);
            $table->string('file_number')->nullable();       // no_rek / Nomor Berkas
            $table->boolean('auto_number')->default(false);

            $table->string('collateral_type_code', 4);       // jenis
            $table->string('binding_type_code', 4)->nullable();
            $table->string('securities_rank')->nullable();   // peringkat_sb (dinonaktifkan)
            $table->string('rating_agency')->nullable();     // pemeringkat_sb (dinonaktifkan)

            $table->string('ownership')->nullable();         // kepemilikan
            $table->string('document_number')->nullable();   // No. SHM / no. dokumen
            $table->text('description')->nullable();         // keterangan

            $table->string('owner_name')->nullable();
            $table->string('owner_address')->nullable();
            $table->boolean('owner_same_as_cif')->default(false);

            $table->string('region_code', 8)->nullable();    // lokasi (dati2)
            $table->string('region_label')->nullable();      // cuplikan nama wilayah

            $table->unsignedBigInteger('value_guarantee')->default(0);   // nilai.jaminan
            $table->unsignedBigInteger('value_adjustment')->default(0);  // nilai.adjust
            $table->unsignedBigInteger('value_fair')->default(0);        // nilai.wajar
            $table->unsignedBigInteger('value_njop')->default(0);        // nilai.njop
            $table->unsignedBigInteger('value_appraisal')->default(0);   // nilai.taksasi
            $table->unsignedBigInteger('value_independent')->default(0); // nilai.independen

            $table->string('appraiser_name')->nullable();
            $table->date('appraised_at')->nullable();
            $table->string('independent_appraiser_name')->nullable();
            $table->date('independent_appraised_at')->nullable();

            $table->string('condition_code', 4)->nullable();
            $table->date('condition_date')->nullable();

            $table->char('insured', 1)->default('T');        // asuransi: Y / T
            $table->string('ppap_code', 4)->default('1');    // metode hitung PPAP
            $table->date('insurance_start_date')->nullable(); // startdate

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collateral_simulations');
    }
};
