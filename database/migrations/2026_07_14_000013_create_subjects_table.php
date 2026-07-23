<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);
            $table->unsignedBigInteger('subject_category_id');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('program_id')->nullable();

            // ── Core Data ─────────────────────────────────────────────
            $table->string('name', 100);
            $table->string('code', 50);
            $table->string('short_name', 20)->nullable();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->string('color', 20)->nullable();
            $table->boolean('is_elective')->default(false);
            $table->boolean('is_core_subject')->default(false);
            $table->unsignedSmallInteger('weekly_hours')->default(1);

            // ── Status ────────────────────────────────────────────────
            $table->string('status', 20)->default('active');

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('subject_category_id')->references('id')->on('subject_categories')->cascadeOnDelete();
            $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
            $table->foreign('program_id')->references('id')->on('programs')->nullOnDelete();
            $table->unique(['school_id', 'name']);
            $table->unique(['school_id', 'code'], 'subjects_school_code_unique');
            $table->index('status');
            $table->index(['school_id', 'display_order']);
            $table->index('subject_category_id');
            $table->index('department_id');
            $table->index('program_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
