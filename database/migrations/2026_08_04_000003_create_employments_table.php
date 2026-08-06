<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('employments', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            // ── References ────────────────────────────────────────────
            $table->foreignId('staff_id')->constrained('staffs')->cascadeOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();

            // ── Contract ──────────────────────────────────────────────
            $table->string('employment_type', 30)->default('full_time');
            $table->string('contract_type', 30)->default('permanent');
            $table->string('status', 20)->default('active');
            $table->string('salary_scale', 20)->nullable();
            $table->decimal('salary_amount', 12, 2)->nullable();

            // ── Period ────────────────────────────────────────────────
            $table->date('contract_start_date');
            $table->date('contract_end_date')->nullable();
            $table->date('termination_date')->nullable();
            $table->text('termination_reason')->nullable();

            // ── Notes ─────────────────────────────────────────────────
            $table->text('notes')->nullable();

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            // At most one active employment per staff: a non-terminated
            // contract has a NULL termination_date.
            $table->unique(['school_id', 'staff_id'])->whereNull('termination_date');
            $table->index(['school_id', 'staff_id']);
            $table->index(['school_id', 'status']);
            $table->index(['staff_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employments');
    }
};
