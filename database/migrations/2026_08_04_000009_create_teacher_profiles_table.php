<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('teacher_profiles', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            // ── References ────────────────────────────────────────────
            $table->foreignId('staff_id')->constrained('staffs')->cascadeOnDelete();

            // ── Teaching profile ──────────────────────────────────────
            $table->string('teaching_license_number', 50)->nullable();
            $table->date('teaching_license_valid_until')->nullable();
            $table->boolean('is_homeroom_teacher')->default(false);
            $table->unsignedSmallInteger('max_weekly_hours')->nullable();
            $table->json('preferred_teaching_levels')->nullable();
            $table->json('teaching_subject_ids')->nullable();
            $table->text('notes')->nullable();

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->unique(['school_id', 'staff_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_profiles');
    }
};
