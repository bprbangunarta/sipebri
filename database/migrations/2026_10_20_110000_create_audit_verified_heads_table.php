<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Each verification that finds the chain intact notes the last entry it checked. The next verification wants that entry to be
 * still there, so entries deleted at the end of the chain (which leave a perfectly linked chain behind) are noticed.
 * Rows are only ever added.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_verified_heads', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->unsignedBigInteger('entry_id')->index();
            $table->char('hash', 64);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_verified_heads');
    }
};
