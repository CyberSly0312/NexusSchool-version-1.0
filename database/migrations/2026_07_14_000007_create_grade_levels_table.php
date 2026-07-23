<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_levels', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->unsignedBigInteger('program_id');

            // ── Core Data ─────────────────────────────────────────────
            $table->string('name', 100);
            $table->string('code', 50);
            $table->string('short_name', 20)->nullable();
            $table->unsignedSmallInteger('level_order');
            $table->string('educational_cycle', 50);
            $table->text('description')->nullable();

            // ── Status ────────────────────────────────────────────────
            $table->string('status', 20)->default('active');

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('program_id')->references('id')->on('programs')->cascadeOnDelete();
            $table->unique(['program_id', 'name']);
            $table->unique(['program_id', 'code'], 'grade_levels_program_code_unique');
            $table->unique(['program_id', 'level_order'], 'grade_levels_program_order_unique');
            $table->index('status');
            $table->index(['program_id', 'level_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_levels');
    }
};
