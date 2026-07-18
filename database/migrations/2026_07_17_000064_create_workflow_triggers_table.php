<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_triggers', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->uuid('id')->primary();
            $table->string('school_id');
            $table->string('workflow_id');

            // ── Trigger Configuration ─────────────────────────────────
            $table->string('type', 30);
            $table->string('name', 200);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            // ── Trigger Conditions ────────────────────────────────────
            $table->string('event_name', 255)->nullable();
            $table->json('conditions')->nullable();
            $table->json('schedule')->nullable();
            $table->json('metadata')->nullable();

            // ── Rate Limiting ─────────────────────────────────────────
            $table->unsignedInteger('cooldown_seconds')->default(0);
            $table->unsignedInteger('max_executions_per_hour')->nullable();

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('workflow_id')->references('id')->on('workflows')->cascadeOnDelete();
            $table->index(['workflow_id', 'is_active']);
            $table->index('type');
            $table->index('event_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_triggers');
    }
};
