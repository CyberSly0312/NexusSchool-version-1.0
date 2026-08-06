<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('time_slots', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            // ── Aggregate reference ───────────────────────────────────
            $table->unsignedBigInteger('timetable_id');
            $table->unsignedBigInteger('teaching_period_id')->nullable();

            // ── Slot location in the weekly grid ──────────────────────
            $table->unsignedTinyInteger('day_of_week');
            $table->unsignedTinyInteger('period_number');
            $table->time('starts_at');
            $table->time('ends_at');

            // ── Validity window (recurring by default) ────────────────
            $table->date('week_start')->nullable();
            $table->date('week_end')->nullable();
            $table->string('label', 100)->nullable();

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('timetable_id')->references('id')->on('timetables')->cascadeOnDelete();
            $table->foreign('teaching_period_id')->references('id')->on('teaching_periods')->nullOnDelete();
            $table->index(['timetable_id', 'day_of_week', 'period_number']);
            $table->index(['school_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_slots');
    }
};
