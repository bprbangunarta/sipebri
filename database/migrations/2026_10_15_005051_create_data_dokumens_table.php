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
        Schema::create('data_analisas_kpmi_dokumen', function (Blueprint $table) {
            $table->id();
            $table->string('pengajuan_kode', 20);

            $table->string('jenis', 50);           // ktp, hasil_mcu, dan seterusnya
            $table->string('nama_asli', 255);      // nama file saat diunggah
            $table->string('path', 500);           // lokasi file di storage
            $table->string('tipe', 100)->nullable(); // mime, misalnya application/pdf
            $table->unsignedInteger('ukuran');     // byte

            $table->string('input_user', 50)->nullable();
            $table->timestamps();

            // satu file per jenis dokumen untuk satu pengajuan; unggah ulang menggantikan
            $table->unique(['pengajuan_kode', 'jenis']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_analisas_kpmi_dokumen');
    }
};
