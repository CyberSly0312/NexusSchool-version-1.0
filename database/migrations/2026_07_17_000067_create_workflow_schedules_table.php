<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_schedules', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->uuid('id')->primary();
            $table->string('school_id');
            $table->string('workflow_id');
            $table->string('trigger_id')->nullable();

            // ── Schedule Configuration ────────────────────────────────
            $table->string('name', 200);
            $table->string('cron_expression', 100);
            $table->string('timezone', 50)->default('UTC');
            $table->boolean('is_active')->default(true);

            // ── Execution Context ─────────────────────────────────────
            $table->json('payload')->nullable();
            $table->json('conditions')->nullable();

            // ── State ─────────────────────────────────────────────────
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->unsignedInteger('total_runs')->default(0);
            $table->unsignedInteger('failed_runs')->default(0);

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('workflow_id')->references('id')->on('workflows')->cascadeOnDelete();
            $table->index(['workflow_id', 'is_active']);
            $table->index(['is_active', 'next_run_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_schedules');
    }
};
