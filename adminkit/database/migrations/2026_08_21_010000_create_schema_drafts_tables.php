<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Rancangan skema tabel (alat developer) — acuan sebelum migration sesungguhnya dibuat. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schema_drafts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('table_name')->unique();
            $table->text('note')->nullable();
            $table->boolean('with_id')->default(true);
            $table->boolean('with_timestamps')->default(true);
            $table->boolean('with_soft_deletes')->default(false);
            $table->timestamps();
        });

        Schema::create('schema_draft_columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schema_draft_id')->constrained('schema_drafts')->cascadeOnDelete();
            $table->unsignedInteger('sort')->default(0);
            $table->string('name');
            $table->string('type')->default('string');
            $table->string('length')->nullable();
            $table->boolean('is_nullable')->default(false);
            $table->string('default_value')->nullable();
            $table->boolean('is_unique')->default(false);
            $table->boolean('is_index')->default(false);
            $table->string('foreign_table')->nullable();
            $table->string('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schema_draft_columns');
        Schema::dropIfExists('schema_drafts');
    }
};
