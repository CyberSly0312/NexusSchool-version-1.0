<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('attendance_session_records', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            // ── Aggregate links ────────────────────────────────────────
            $table->unsignedBigInteger('attendance_session_id');
            $table->unsignedBigInteger('enrollment_id');

            // ── Attendance data ────────────────────────────────────────
            $table->string('status', 30);
            $table->dateTime('check_in_time')->nullable();
            $table->dateTime('check_out_time')->nullable();
            $table->unsignedSmallInteger('late_minutes')->nullable();
            $table->dateTime('recorded_at');
            $table->text('notes')->nullable();

            // ── Ownership ─────────────────────────────────────────────
            $table->unsignedBigInteger('recorded_by')->nullable();

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('attendance_session_id')->references('id')->on('attendance_sessions')->cascadeOnDelete();
            $table->foreign('enrollment_id')->references('id')->on('enrollments')->cascadeOnDelete();
            $table->foreign('recorded_by')->references('id')->on('users')->nullOnDelete();
            $table->unique(['attendance_session_id', 'enrollment_id'], 'attendance_records_session_enrollment_unique');
            $table->index('status');
            $table->index(['school_id', 'attendance_session_id']);
            $table->index(['school_id', 'enrollment_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_session_records');
    }
};
