<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_analisas_kpmi_amaterasu', function (Blueprint $table) {
            $table->id();
            $table->string('pengajuan_kode', 20)->unique();

            // Isian analis (rupiah per bulan)
            $table->decimal('skor_lendana', 5, 1)->nullable();
            $table->unsignedBigInteger('penghasilan')->nullable();
            $table->unsignedBigInteger('biaya_hidup')->nullable();
            $table->unsignedBigInteger('angsuran_berjalan')->nullable();

            // Angsuran kredit saat analisa dibuat (dari plafon, bunga, tenor pengajuan)
            $table->unsignedBigInteger('angsuran_kredit');

            // Hasil hitung
            $table->decimal('dsr', 8, 4)->nullable();
            $table->bigInteger('sisa_penghasilan')->nullable();
            $table->bigInteger('sisa_penghasilan_stress')->nullable();
            $table->unsignedTinyInteger('nilai_dsr');
            $table->unsignedTinyInteger('nilai_stress');
            $table->decimal('skor_total', 5, 2);
            $table->unsignedTinyInteger('syarat_terpenuhi');
            $table->unsignedTinyInteger('syarat_total');
            $table->string('rekomendasi', 40);

            $table->text('catatan')->nullable();
            $table->string('input_user', 50)->nullable();
            $table->string('update_user', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_analisas_kpmi_amaterasu');
    }
};
