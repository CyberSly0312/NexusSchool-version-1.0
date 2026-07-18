<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_rules', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->uuid('id')->primary();
            $table->string('school_id');

            // ── Core Data ─────────────────────────────────────────────
            $table->string('name', 200);
            $table->text('description')->nullable();
            $table->string('category', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('priority')->default(0);

            // ── Trigger Configuration ─────────────────────────────────
            $table->string('trigger_type', 30);
            $table->string('event_name', 255)->nullable();
            $table->json('conditions')->nullable();

            // ── Actions ───────────────────────────────────────────────
            $table->json('actions')->default('[]');

            // ── Execution Stats ───────────────────────────────────────
            $table->unsignedInteger('execution_count')->default(0);
            $table->unsignedInteger('success_count')->default(0);
            $table->unsignedInteger('failure_count')->default(0);
            $table->timestamp('last_executed_at')->nullable();

            // ── Cooldown ──────────────────────────────────────────────
            $table->unsignedInteger('cooldown_seconds')->default(0);

            // ── Ownership ─────────────────────────────────────────────
            $table->string('created_by')->nullable();

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->index(['school_id', 'is_active']);
            $table->index(['school_id', 'trigger_type']);
            $table->index('event_name');
            $table->index('priority');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_rules');
    }
};
