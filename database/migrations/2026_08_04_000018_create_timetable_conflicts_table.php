<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('timetable_conflicts', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            // ── Aggregate reference ───────────────────────────────────
            $table->unsignedBigInteger('timetable_id');
            $table->string('detection_id', 36);
            $table->string('conflict_type', 40);

            // ── Involved entries ──────────────────────────────────────
            $table->unsignedBigInteger('entry_id')->nullable();
            $table->unsignedBigInteger('conflicting_entry_id')->nullable();

            // ── Context snapshot ──────────────────────────────────────
            $table->string('target_type', 20)->nullable();
            $table->string('target_id', 36)->nullable();
            $table->unsignedTinyInteger('day_of_week')->nullable();
            $table->time('starts_at')->nullable();
            $table->time('ends_at')->nullable();
            $table->text('reason')->nullable();

            // ── Resolution ────────────────────────────────────────────
            $table->timestamp('resolved_at')->nullable();
            $table->string('resolution', 50)->nullable();
            $table->text('resolution_notes')->nullable();

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('timetable_id')->references('id')->on('timetables')->cascadeOnDelete();
            $table->foreign('entry_id')->references('id')->on('schedule_entries')->cascadeOnDelete();
            $table->foreign('conflicting_entry_id')->references('id')->on('schedule_entries')->cascadeOnDelete();
            $table->index(['timetable_id', 'detection_id']);
            $table->index(['timetable_id', 'conflict_type']);
            $table->index(['timetable_id', 'resolved_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetable_conflicts');
    }
};
