<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * When old audit entries are pruned, the hash of the last removed entry is kept here so the first
 * remaining entry still has something to link to. Each pruning is also written to the audit trail itself.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_anchors', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->unsignedBigInteger('pruned_through_id');
            $table->unsignedInteger('pruned_count');
            $table->char('last_hash', 64)->unique();
            $table->timestamp('cutoff');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_anchors');
    }
};
