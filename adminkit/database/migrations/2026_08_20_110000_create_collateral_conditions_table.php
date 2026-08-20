<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Kondisi agunan (fasum, sengketa, dll) — mengikuti data core banking (CBS). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collateral_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collateral_conditions');
    }
};
