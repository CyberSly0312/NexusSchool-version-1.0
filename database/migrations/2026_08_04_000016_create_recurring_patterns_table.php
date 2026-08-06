<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('recurring_patterns', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            // ── References ────────────────────────────────────────────
            $table->unsignedBigInteger('timetable_id');
            $table->unsignedBigInteger('schedule_entry_id')->nullable();
            $table->unsignedBigInteger('time_slot_id')->nullable();

            // ── Recurrence definition ─────────────────────────────────
            $table->string('frequency', 20)->default('weekly');
            $table->unsignedTinyInteger('interval')->default(1);
            $table->json('occurs_on_days')->nullable();
            $table->date('starts_on');
            $table->date('ends_on')->nullable();
            $table->unsignedInteger('end_after_occurrences')->nullable();
            $table->json('exceptions')->nullable();

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('timetable_id')->references('id')->on('timetables')->cascadeOnDelete();
            $table->foreign('schedule_entry_id')->references('id')->on('schedule_entries')->cascadeOnDelete();
            $table->foreign('time_slot_id')->references('id')->on('time_slots')->cascadeOnDelete();
            $table->index(['timetable_id', 'frequency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_patterns');
    }
};
