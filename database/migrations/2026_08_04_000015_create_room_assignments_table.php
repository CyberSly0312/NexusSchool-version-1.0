<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('room_assignments', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            // ── References ────────────────────────────────────────────
            $table->unsignedBigInteger('timetable_id');
            $table->unsignedBigInteger('schedule_entry_id');

            // ── Room (owned by the future Room Management engine) ────
            $table->string('room_id', 36);

            // ── Weekly grid position (snapshot at assignment time) ────
            $table->unsignedTinyInteger('day_of_week');
            $table->unsignedTinyInteger('period_number');
            $table->time('starts_at');
            $table->time('ends_at');
            $table->string('purpose', 30)->default('lesson');

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('timetable_id')->references('id')->on('timetables')->cascadeOnDelete();
            $table->foreign('schedule_entry_id')->references('id')->on('schedule_entries')->cascadeOnDelete();
            $table->unique(['schedule_entry_id', 'room_id']);
            $table->index(['room_id', 'day_of_week']);
            $table->index(['timetable_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_assignments');
    }
};
