<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->unsignedBigInteger('grade_level_id');

            // ── Core Data ─────────────────────────────────────────────
            $table->string('name', 100);
            $table->string('code', 50);
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('order')->default(0);

            // ── Status ────────────────────────────────────────────────
            $table->string('status', 20)->default('active');

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('grade_level_id')->references('id')->on('grade_levels')->cascadeOnDelete();
            $table->unique(['grade_level_id', 'name']);
            $table->unique(['grade_level_id', 'code'], 'sections_grade_level_code_unique');
            $table->index('status');
            $table->index(['grade_level_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
