<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Codex is the source of truth for offices, so a user points at a row of `offices` instead of carrying
 * the office name and code as text. Offices also keep the extra data Codex reports.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            // Offices created from Codex have no alias until someone sets one.
            $table->string('alias', 8)->nullable()->change();
            $table->string('address')->nullable();
            $table->string('telephone', 30)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('radius')->nullable();
            $table->string('coa', 30)->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('office_id')->nullable()->after('username')->constrained()->restrictOnDelete();
        });

        // Keep what is already stored: match by office code, then by name.
        foreach (DB::table('users')->whereNotNull('office')->orWhereNotNull('office_code')->get(['id', 'office', 'office_code']) as $user) {
            $officeId = DB::table('offices')->where('code', $user->office_code)->value('id')
                ?? DB::table('offices')->where('name', $user->office)->value('id');

            if ($officeId !== null) {
                DB::table('users')->where('id', $user->id)->update(['office_id' => $officeId]);
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['office', 'office_code']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('office')->nullable();
            $table->string('office_code', 10)->nullable();
        });

        DB::table('users')->whereNotNull('office_id')->update([
            'office' => DB::raw('(select name from offices where offices.id = users.office_id)'),
            'office_code' => DB::raw('(select code from offices where offices.id = users.office_id)'),
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('office_id');
        });

        Schema::table('offices', function (Blueprint $table) {
            $table->dropColumn(['address', 'telephone', 'latitude', 'longitude', 'radius', 'coa']);
        });
    }
};
