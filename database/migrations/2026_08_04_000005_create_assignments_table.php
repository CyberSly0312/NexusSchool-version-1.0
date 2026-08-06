<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            // ── References ────────────────────────────────────────────
            $table->foreignId('staff_id')->constrained('staffs')->cascadeOnDelete();
            $table->foreignId('employment_id')->nullable()->constrained('employments')->nullOnDelete();

            // ── Assignment payload ────────────────────────────────────
            $table->string('assignment_type', 30);
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->string('campus_school_id', 36)->nullable();

            // ── Time-aware validity window ────────────────────────────
            $table->date('assignment_start_date');
            $table->date('assignment_end_date')->nullable();
            $table->boolean('is_current')->default(true);
            $table->text('notes')->nullable();

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->index(['school_id', 'staff_id', 'assignment_type']);
            $table->index(['school_id', 'staff_id', 'is_current']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
