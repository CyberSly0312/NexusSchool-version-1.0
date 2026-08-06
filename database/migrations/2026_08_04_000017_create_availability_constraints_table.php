<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('availability_constraints', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            // ── Aggregate reference ───────────────────────────────────
            $table->unsignedBigInteger('timetable_id');
            $table->unsignedBigInteger('time_slot_id')->nullable();

            // ── Constraint target (teacher / room / class) ────────────
            $table->string('constraint_type', 20);
            $table->string('target_type', 20);
            $table->string('target_id', 36);

            // ── Constraint window ─────────────────────────────────────
            $table->unsignedTinyInteger('day_of_week')->nullable();
            $table->time('starts_at')->nullable();
            $table->time('ends_at')->nullable();
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->string('reason', 255)->nullable();

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('timetable_id')->references('id')->on('timetables')->cascadeOnDelete();
            $table->foreign('time_slot_id')->references('id')->on('time_slots')->cascadeOnDelete();
            $table->index(['target_type', 'target_id']);
            $table->index(['timetable_id', 'constraint_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('availability_constraints');
    }
};
