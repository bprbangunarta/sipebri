<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Two-factor settings are local to this system (Codex knows nothing about them).
     * `mfa_method` is "totp" or "email"; it only counts once `mfa_confirmed_at` is set.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mfa_method', 10)->nullable();
            $table->text('mfa_secret')->nullable();
            $table->text('mfa_recovery_codes')->nullable();
            $table->timestamp('mfa_confirmed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['mfa_method', 'mfa_secret', 'mfa_recovery_codes', 'mfa_confirmed_at']);
        });
    }
};
