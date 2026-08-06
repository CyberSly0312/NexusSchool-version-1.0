<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('attendance_sessions', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            // ── Academic framework references (via contracts) ────────
            $table->unsignedBigInteger('academic_calendar_year_id')->nullable();
            $table->unsignedBigInteger('academic_calendar_term_id')->nullable();
            $table->unsignedBigInteger('school_class_id')->nullable();
            $table->unsignedBigInteger('class_section_id')->nullable();
            $table->unsignedBigInteger('academic_subject_id')->nullable();

            // ── Timetable reference (via contract) ────────────────────
            $table->unsignedBigInteger('timetable_id')->nullable();
            $table->unsignedBigInteger('schedule_entry_id')->nullable();

            // ── Time slot ─────────────────────────────────────────────
            $table->date('date');
            $table->unsignedTinyInteger('day_of_week');
            $table->unsignedSmallInteger('period_number');
            $table->time('starts_at')->nullable();
            $table->time('ends_at')->nullable();

            // ── Teaching staff (via contract) ─────────────────────────
            $table->unsignedBigInteger('staff_id')->nullable();

            // ── State ─────────────────────────────────────────────────
            $table->string('status', 30)->default('draft');
            $table->unsignedInteger('expected_students_count')->default(0);
            $table->unsignedInteger('recorded_students_count')->default(0);
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->timestamp('archived_at')->nullable();

            // ── Ownership ─────────────────────────────────────────────
            $table->unsignedBigInteger('created_by')->nullable();
            $table->text('notes')->nullable();

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('school_class_id')->references('id')->on('school_classes')->cascadeOnDelete();
            $table->foreign('class_section_id')->references('id')->on('class_sections')->cascadeOnDelete();
            $table->foreign('academic_subject_id')->references('id')->on('academic_subjects')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->unique(['school_id', 'school_class_id', 'date', 'day_of_week', 'period_number'], 'attendance_sessions_class_slot_unique');
            $table->index('status');
            $table->index(['school_id', 'date']);
            $table->index(['school_id', 'status', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_sessions');
    }
};
