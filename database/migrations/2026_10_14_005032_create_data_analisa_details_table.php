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
        Schema::create('data_analisas_kpmi_detail_amaterasu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analisa_id')
                ->constrained('data_analisas_kpmi_amaterasu')
                ->cascadeOnDelete();

            $table->string('kriteria', 50);
            $table->string('jawaban', 10)->nullable(); // Ya / Tidak, khusus syarat
            $table->unsignedTinyInteger('nilai');      // 0-5
            $table->unsignedTinyInteger('bobot');      // bobot saat analisa dibuat
            $table->decimal('skor', 5, 2);             // bobot x nilai / 5
            $table->boolean('wajib')->default(false);  // termasuk syarat wajib
            $table->timestamps();

            $table->unique(['analisa_id', 'kriteria']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_analisas_kpmi_detail_amaterasu');
    }
};
