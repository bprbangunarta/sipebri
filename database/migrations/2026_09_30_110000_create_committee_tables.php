<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Credit committee rules: one path per product + condition, made of ordered tiers that define
 * the deciding role, the amount range and the decisions allowed.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('committee_paths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('condition', 30)->nullable();
            $table->string('mechanism', 20)->default('plafon');
            $table->boolean('is_active')->default(true);
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'condition']);
        });

        Schema::create('committee_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('committee_path_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sort')->default(0);
            $table->string('label', 50)->nullable();
            $table->string('role', 100);
            $table->unsignedBigInteger('min_amount')->nullable();
            $table->unsignedBigInteger('max_amount')->nullable();
            $table->boolean('can_escalate')->default(false);
            $table->boolean('can_approve')->default(false);
            $table->boolean('can_cancel')->default(false);
            $table->boolean('can_reject')->default(false);
            $table->timestamps();

            $table->index(['committee_path_id', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('committee_tiers');
        Schema::dropIfExists('committee_paths');
    }
};
