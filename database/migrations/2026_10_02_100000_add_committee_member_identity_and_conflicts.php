<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A committee member who applies for a credit cannot take part in deciding it, so the system has to recognise them from the
 * applicant's national ID (KTP). The NIK is kept encrypted, with a keyed hash next to it to look people up without decrypting
 * everyone; it comes from Codex when Codex sends it, or is typed by a Super Admin until then. A file remembers which committee member
 * (if any) is the applicant, and how that was found out.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('nik')->nullable()->after('email');
            $table->char('nik_hash', 64)->nullable()->index()->after('nik');
            $table->string('nik_source', 10)->nullable()->after('nik_hash');
        });

        Schema::table('loan_applications', function (Blueprint $table) {
            $table->foreignId('committee_conflict_user_id')->nullable()->after('note')->constrained('users')->nullOnDelete();
            $table->string('committee_conflict_source', 10)->nullable()->after('committee_conflict_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('committee_conflict_user_id');
            $table->dropColumn('committee_conflict_source');
        });

        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['nik', 'nik_hash', 'nik_source']));
    }
};
