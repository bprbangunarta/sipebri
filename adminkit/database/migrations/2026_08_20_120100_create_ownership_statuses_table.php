<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Status/bukti kepemilikan agunan — pilihannya berbeda per jenis agunan (CBS). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ownership_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('collateral_type_code', 4);
            $table->string('code', 4);
            $table->string('name');
            $table->timestamps();

            $table->unique(['collateral_type_code', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ownership_statuses');
    }
};
