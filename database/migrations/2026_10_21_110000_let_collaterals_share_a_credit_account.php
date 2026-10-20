<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One credit account holds several collaterals (a house and a car for the same loan), so the account number on a collateral cannot be unique.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->dropUnique(['credit_account']);
            $table->index('credit_account');
        });
    }

    public function down(): void
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->dropIndex(['credit_account']);
            $table->unique('credit_account');
        });
    }
};
