<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_terms', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->unsignedBigInteger('academic_year_id');
            $table->string('school_id');

            // ── Core Data ─────────────────────────────────────────────
            $table->string('name', 100);
            $table->string('code', 50)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedSmallInteger('order')->default(0);
            $table->text('description')->nullable();

            // ── Status ────────────────────────────────────────────────
            $table->string('status', 20)->default('draft');

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->unique(['academic_year_id', 'name']);
            $table->unique(['academic_year_id', 'code'], 'academic_terms_year_code_unique');
            $table->index('status');
            $table->index(['academic_year_id', 'start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_terms');
    }
};
