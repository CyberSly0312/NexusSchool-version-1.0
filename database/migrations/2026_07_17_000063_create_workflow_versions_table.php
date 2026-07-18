<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_versions', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->uuid('id')->primary();
            $table->string('school_id');
            $table->string('workflow_id');

            // ── Versioning ────────────────────────────────────────────
            $table->unsignedInteger('version');
            $table->string('status', 30)->default('draft');
            $table->text('changelog')->nullable();

            // ── Graph Definition ──────────────────────────────────────
            $table->json('nodes')->default('[]');
            $table->json('edges')->default('[]');
            $table->json('settings')->nullable();

            // ── Publishing ────────────────────────────────────────────
            $table->string('published_by')->nullable();
            $table->timestamp('published_at')->nullable();

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('workflow_id')->references('id')->on('workflows')->cascadeOnDelete();
            $table->unique(['workflow_id', 'version']);
            $table->index(['workflow_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_versions');
    }
};
