<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_classes', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->id();
            $table->ulid('uuid')->unique();
            $table->unsignedBigInteger('academic_year_id');
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('grade_level_id');
            $table->unsignedBigInteger('section_id');

            // ── Core Data ─────────────────────────────────────────────
            $table->string('name', 100);
            $table->string('code', 50);
            $table->text('description')->nullable();
            $table->unsignedInteger('capacity')->default(0);
            $table->unsignedSmallInteger('display_order')->default(0);

            // ── Status ────────────────────────────────────────────────
            $table->string('status', 20)->default('active');
            $table->boolean('is_active')->default(true);

            // ── Future References (nullable) ──────────────────────────
            $table->unsignedBigInteger('homeroom_teacher_id')->nullable();
            $table->string('classroom', 100)->nullable();

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
            $table->foreign('program_id')->references('id')->on('programs')->cascadeOnDelete();
            $table->foreign('grade_level_id')->references('id')->on('grade_levels')->cascadeOnDelete();
            $table->foreign('section_id')->references('id')->on('sections')->cascadeOnDelete();
            $table->unique(['academic_year_id', 'name']);
            $table->unique('code', 'academic_classes_code_unique');
            $table->index('status');
            $table->index(['academic_year_id', 'display_order']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_classes');
    }
};
