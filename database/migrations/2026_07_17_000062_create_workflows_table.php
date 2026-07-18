<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflows', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->uuid('id')->primary();
            $table->string('school_id');

            // ── Core Data ─────────────────────────────────────────────
            $table->string('name', 200);
            $table->string('slug', 220);
            $table->text('description')->nullable();
            $table->string('category', 100)->nullable();
            $table->unsignedSmallInteger('priority')->default(0);

            // ── Status ────────────────────────────────────────────────
            $table->string('status', 30)->default('draft');
            $table->boolean('is_active')->default(true);

            // ── Versioning ────────────────────────────────────────────
            $table->unsignedInteger('current_version')->default(1);

            // ── Configuration ─────────────────────────────────────────
            $table->json('metadata')->nullable();
            $table->json('settings')->nullable();

            // ── Ownership ─────────────────────────────────────────────
            $table->string('created_by')->nullable();

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->unique(['school_id', 'slug']);
            $table->index('status');
            $table->index('category');
            $table->index(['school_id', 'is_active']);
            $table->index(['school_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflows');
    }
};
