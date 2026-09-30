<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Users are mirrored from the Codex sign-in API: the local id is the Codex id, "active" is the soft
     * delete, and the local password is never used to sign in.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('office')->nullable();
            $table->string('office_code', 10)->nullable();
            $table->string('alias', 20)->nullable()->unique();
            $table->string('mso_code')->nullable();
            $table->string('collector_code')->nullable()->unique();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropUnique(['alias']);
            $table->dropUnique(['collector_code']);
            $table->dropSoftDeletes();
            $table->dropColumn(['username', 'office', 'office_code', 'alias', 'mso_code', 'collector_code']);
        });
    }
};
