<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_tasks', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->uuid('id')->primary();
            $table->string('school_id');
            $table->string('workflow_execution_id');
            $table->string('node_id', 100);

            // ── Task Configuration ────────────────────────────────────
            $table->string('type', 30);
            $table->string('name', 200);
            $table->string('status', 30)->default('pending');
            $table->unsignedSmallInteger('order')->default(0);

            // ── Assignment ────────────────────────────────────────────
            $table->string('assigned_to')->nullable();
            $table->string('assigned_to_type', 50)->nullable();
            $table->string('assigned_by')->nullable();
            $table->timestamp('assigned_at')->nullable();

            // ── Execution ─────────────────────────────────────────────
            $table->json('input_data')->nullable();
            $table->json('output_data')->nullable();
            $table->text('notes')->nullable();
            $table->text('error')->nullable();

            // ── Timing ────────────────────────────────────────────────
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('workflow_execution_id')->references('id')->on('workflow_executions')->cascadeOnDelete();
            $table->index(['workflow_execution_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index('due_at');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_tasks');
    }
};
