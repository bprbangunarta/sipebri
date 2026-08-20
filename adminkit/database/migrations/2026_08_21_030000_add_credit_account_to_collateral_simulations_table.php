<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Nomor rekening kredit pada agunan. Diisi otomatis setelah posting data kredit
 * ke core banking dan menerima nomor rekening dari respons CBS.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('collateral_simulations', function (Blueprint $table) {
            $table->string('credit_account', 30)->nullable()->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('collateral_simulations', function (Blueprint $table) {
            $table->dropUnique(['credit_account']);
            $table->dropColumn('credit_account');
        });
    }
};
