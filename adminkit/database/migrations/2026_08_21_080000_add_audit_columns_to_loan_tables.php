<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Konvensi kolom audit: created_by (nama pengguna), updated_by, deleted_by + softDeletes.
 * Kolom confirmed_by/confirmed_at dilepas — pengajuan cukup ditandai perubahan status DRAFT → DIAJUKAN.
 */
return new class extends Migration
{
    public function up(): void
    {
        $names = DB::table('users')->pluck('name', 'id');

        $legacy = DB::table('loan_applications')
            ->whereNotNull('created_by')
            ->pluck('created_by', 'id');

        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['confirmed_by']);
        });

        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'confirmed_by', 'confirmed_at']);
        });

        Schema::table('loan_applications', function (Blueprint $table) {
            $table->string('created_by')->default('SISTEM');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });

        foreach ($legacy as $id => $userId) {
            DB::table('loan_applications')
                ->where('id', $id)
                ->update(['created_by' => $names[$userId] ?? 'SISTEM']);
        }

        Schema::table('collateral_simulations', function (Blueprint $table) {
            $table->string('created_by')->default('SISTEM');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by', 'deleted_by']);
        });

        Schema::table('loan_applications', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('confirmed_by')->nullable();
            $table->timestamp('confirmed_at')->nullable();
        });

        Schema::table('collateral_simulations', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by', 'deleted_by', 'deleted_at']);
        });
    }
};
