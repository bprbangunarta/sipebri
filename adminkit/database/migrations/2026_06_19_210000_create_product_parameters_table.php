<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Parameter produk (SK Direksi): batas plafon, tenor, bunga & biaya, metode
 * yang diizinkan, ambang RC, dan kewajiban agunan. Satu baris per produk.
 * Dipisah dari tabel `products` agar master produk tetap cermin core banking.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('min_amount')->nullable();
            $table->unsignedBigInteger('max_amount')->nullable();
            $table->unsignedSmallInteger('min_tenor')->nullable();
            $table->unsignedSmallInteger('max_tenor')->nullable();
            $table->decimal('interest_rate', 6, 3)->nullable();
            $table->decimal('provision_rate', 6, 3)->nullable();
            $table->decimal('admin_rate', 6, 3)->nullable();
            $table->decimal('rc_threshold', 5, 2)->nullable();
            $table->foreignId('default_method_id')->nullable()->constrained('methods')->nullOnDelete();
            $table->foreignId('default_installment_id')->nullable()->constrained('installments')->nullOnDelete();
            $table->json('allowed_method_ids')->nullable();
            $table->json('allowed_installment_ids')->nullable();
            $table->boolean('collateral_required')->default(false);
            $table->string('decree')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_parameters');
    }
};
