<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Referensi wilayah (dati2 → kecamatan → kelurahan) untuk lokasi agunan. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 8);          // kode dati2 yang dikirim ke CBS
            $table->string('regency');
            $table->string('district');
            $table->string('village');
            $table->string('postal_code', 8)->nullable();
            $table->timestamps();

            $table->index('code');
            $table->index(['regency', 'district']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
