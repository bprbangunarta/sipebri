<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Aturan komite kredit: satu jalur per kombinasi produk + kondisi/kategori,
 * dengan jenjang (tier) yang menentukan peranan pemutus, batas plafon, dan
 * keputusan yang diizinkan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('committee_paths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('condition')->nullable();
            $table->string('mechanism')->default('plafon');
            $table->boolean('is_active')->default(true);
            $table->string('note')->nullable();
            $table->timestamps();
        });

        Schema::create('committee_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('committee_path_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sort')->default(0);
            $table->string('label')->nullable();
            $table->string('role');
            $table->unsignedBigInteger('min_amount')->nullable();
            $table->unsignedBigInteger('max_amount')->nullable();
            $table->boolean('can_escalate')->default(false);
            $table->boolean('can_approve')->default(false);
            $table->boolean('can_cancel')->default(false);
            $table->boolean('can_reject')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('committee_tiers');
        Schema::dropIfExists('committee_paths');
    }
};
