<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->string('code', 8)->unique();
            $table->string('alias', 8)->unique();
            $table->string('name', 100);
            $table->timestamps();
        });

        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 8)->unique();
            $table->string('name', 100);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code', 8)->unique();
            $table->string('alias', 12)->unique();
            $table->string('name', 100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->string('code', 8)->unique();
            $table->string('name', 100);
            $table->unsignedSmallInteger('period_months')->default(1);
            $table->timestamps();
        });

        foreach (['methods', 'collateral_types', 'binding_types', 'collateral_conditions', 'collateral_methods'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->string('code', 8)->unique();
                $table->string('name', 100);
                $table->timestamps();
            });
        }

        Schema::create('ownership_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('collateral_type_code', 8);
            $table->string('code', 8);
            $table->string('name', 100);
            $table->timestamps();

            $table->unique(['collateral_type_code', 'code']);
        });

        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 8)->index();
            $table->string('regency', 100);
            $table->string('district', 100);
            $table->string('village', 100);
            $table->string('postal_code', 8)->nullable();
            $table->timestamps();

            $table->index(['regency', 'district']);
        });

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
            $table->string('decree', 150)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['product_parameters', 'regions', 'ownership_statuses', 'collateral_methods', 'collateral_conditions', 'binding_types', 'collateral_types', 'methods', 'installments', 'products', 'institutions', 'offices'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
