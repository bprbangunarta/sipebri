<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Kolom yang tidak dipakai pada form/payload agunan dihapus (tinjauan skema 2026-06-21). */
return new class extends Migration
{
    private array $columns = ['paripasu', 'file_number', 'auto_number', 'ownership', 'owner_same_as_cif', 'region_id'];

    public function up(): void
    {
        Schema::table('collateral_simulations', function (Blueprint $table) {
            $table->dropColumn($this->columns);
        });
    }

    public function down(): void
    {
        Schema::table('collateral_simulations', function (Blueprint $table) {
            $table->unsignedTinyInteger('paripasu')->default(0);
            $table->string('file_number')->nullable();
            $table->boolean('auto_number')->default(false);
            $table->string('ownership')->nullable();
            $table->boolean('owner_same_as_cif')->default(false);
            $table->unsignedBigInteger('region_id')->nullable();
        });
    }
};
