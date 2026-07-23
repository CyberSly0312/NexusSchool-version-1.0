<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subject_coefficients', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('grade_level_id');
            $table->unsignedBigInteger('academic_year_id')->nullable();

            // ── Core Data ─────────────────────────────────────────────
            $table->decimal('coefficient', 5, 2);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->text('notes')->nullable();

            // ── Status ────────────────────────────────────────────────
            $table->string('status', 20)->default('active');

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('subject_id')->references('id')->on('subjects')->cascadeOnDelete();
            $table->foreign('program_id')->references('id')->on('programs')->cascadeOnDelete();
            $table->foreign('grade_level_id')->references('id')->on('grade_levels')->cascadeOnDelete();
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->nullOnDelete();

            $table->unique(['school_id', 'subject_id', 'program_id', 'grade_level_id', 'academic_year_id', 'status'], 'subject_coefficients_unique_active');
            $table->index(['school_id', 'subject_id']);
            $table->index(['school_id', 'program_id']);
            $table->index(['school_id', 'grade_level_id']);
            $table->index(['school_id', 'academic_year_id']);
            $table->index(['school_id', 'status']);
            $table->index(['effective_from', 'effective_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_coefficients');
    }
};
