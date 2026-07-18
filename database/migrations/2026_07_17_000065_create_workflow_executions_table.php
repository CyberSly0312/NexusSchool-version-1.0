<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_executions', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->uuid('id')->primary();
            $table->string('school_id');
            $table->string('workflow_id');
            $table->string('workflow_version_id');
            $table->string('trigger_id')->nullable();

            // ── Execution Data ────────────────────────────────────────
            $table->string('status', 30)->default('pending');
            $table->string('current_node_id', 100)->nullable();
            $table->json('context')->nullable();
            $table->json('variables')->nullable();
            $table->json('result')->nullable();
            $table->text('error')->nullable();

            // ── Timing ────────────────────────────────────────────────
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();

            // ── Concurrency ───────────────────────────────────────────
            $table->unsignedSmallInteger('retry_count')->default(0);
            $table->unsignedSmallInteger('max_retries')->default(3);
            $table->string('idempotency_key', 255)->nullable();

            // ── Ownership ─────────────────────────────────────────────
            $table->string('initiated_by')->nullable();
            $table->string('initiated_by_type', 50)->nullable();

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('workflow_id')->references('id')->on('workflows')->cascadeOnDelete();
            $table->foreign('workflow_version_id')->references('id')->on('workflow_versions')->cascadeOnDelete();
            $table->unique(['school_id', 'idempotency_key'], 'wf_exec_idempotency_unique');
            $table->index(['workflow_id', 'status']);
            $table->index(['school_id', 'status']);
            $table->index('started_at');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_executions');
    }
};
