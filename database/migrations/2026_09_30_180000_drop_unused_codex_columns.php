<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The Codex API serves several systems. This one only keeps what it uses: for a user the id, username,
 * name, email, office, role and active state; for an office the code, alias and name.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['alias']);
            $table->dropUnique(['collector_code']);
            $table->dropColumn(['birthday', 'alias', 'mso_code', 'collector_code']);
        });

        Schema::table('offices', function (Blueprint $table) {
            $table->dropColumn(['address', 'telephone', 'latitude', 'longitude', 'radius', 'coa']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('birthday')->nullable();
            $table->string('alias', 20)->nullable()->unique();
            $table->string('mso_code')->nullable();
            $table->string('collector_code')->nullable()->unique();
        });

        Schema::table('offices', function (Blueprint $table) {
            $table->string('address')->nullable();
            $table->string('telephone', 30)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('radius')->nullable();
            $table->string('coa', 30)->nullable();
        });
    }
};
