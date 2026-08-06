<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('schedule_entries', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            // ── Aggregate reference ───────────────────────────────────
            $table->unsignedBigInteger('timetable_id');
            $table->unsignedBigInteger('time_slot_id')->nullable();
            $table->unsignedBigInteger('parent_entry_id')->nullable();

            // ── Academic framework references (via contracts) ────────
            $table->unsignedBigInteger('academic_subject_id');
            $table->unsignedBigInteger('school_class_id');
            $table->unsignedBigInteger('class_section_id')->nullable();
            $table->unsignedBigInteger('staff_id')->nullable();

            // ── Room (owned by the future Room Management engine) ────
            $table->string('room_id', 36)->nullable();

            // ── Weekly grid position ──────────────────────────────────
            $table->unsignedTinyInteger('day_of_week');
            $table->unsignedTinyInteger('period_number');
            $table->time('starts_at');
            $table->time('ends_at');
            $table->unsignedInteger('duration_minutes');

            // ── Behavior ──────────────────────────────────────────────
            $table->string('entry_type', 30)->default('lesson');
            $table->string('status', 20)->default('scheduled');
            $table->boolean('is_recurring')->default(false);
            $table->text('notes')->nullable();

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('timetable_id')->references('id')->on('timetables')->cascadeOnDelete();
            $table->foreign('time_slot_id')->references('id')->on('time_slots')->nullOnDelete();
            $table->foreign('parent_entry_id')->references('id')->on('schedule_entries')->cascadeOnDelete();
            $table->foreign('academic_subject_id')->references('id')->on('academic_subjects')->restrictOnDelete();
            $table->foreign('school_class_id')->references('id')->on('school_classes')->cascadeOnDelete();
            $table->foreign('class_section_id')->references('id')->on('class_sections')->cascadeOnDelete();
            $table->foreign('staff_id')->references('id')->on('staffs')->nullOnDelete();
            $table->index(['timetable_id', 'day_of_week']);
            $table->index(['timetable_id', 'status']);
            $table->index(['school_class_id', 'day_of_week']);
            $table->index(['staff_id', 'day_of_week']);
            $table->index(['school_id', 'day_of_week']);
        });

        // Enforce "a class is never scheduled twice in the same period within a
        // timetable" at the database level where the driver supports partial
        // indexes. Supersession chains (reschedule/cancel) are exempt because
        // their parent row exists. The index is scoped to the timetable so
        // separate timetables (different terms, duplicates, next-year drafts)
        // may reuse the same weekly slots; actions enforce the one-published-per
        // period invariant on top.
        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['sqlite', 'pgsql', 'mariadb'], true)) {
            DB::statement(
                'CREATE UNIQUE INDEX schedule_entries_class_slot ON schedule_entries '
                . '(school_id, timetable_id, day_of_week, period_number, school_class_id) '
                . 'WHERE parent_entry_id IS NULL AND deleted_at IS NULL'
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_entries');
    }
};
