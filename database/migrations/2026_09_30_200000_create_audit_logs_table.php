<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Append-only audit trail. Each row carries a keyed hash of its own content and of the previous row, so any
 * later change or removal breaks the chain (see `php artisan audit:verify`). The user is stored as a snapshot,
 * without a foreign key, so the trail survives whatever happens to the user record.
 * Values are kept as text (not JSON columns) so the exact bytes that were hashed are the bytes that are read back.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at', 6)->index();
            $table->string('request_id', 36)->index();

            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('user_name', 150)->nullable();
            $table->string('username', 100)->nullable();

            $table->string('event', 80)->index();
            $table->string('module', 50);
            $table->string('action', 30);
            $table->string('outcome', 10)->default('success');

            $table->string('subject_type', 100)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_label', 255)->nullable();

            $table->longText('old_values')->nullable();
            $table->longText('new_values')->nullable();
            $table->longText('context')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->string('http_method', 10)->nullable();
            $table->string('url', 500)->nullable();

            $table->char('previous_hash', 64)->nullable();
            $table->char('hash', 64)->unique();

            $table->index(['subject_type', 'subject_id']);
            $table->index(['module', 'created_at']);
            $table->index(['outcome', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
