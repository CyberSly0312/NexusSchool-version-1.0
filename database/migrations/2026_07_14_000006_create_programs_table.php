<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id');
            $table->unsignedBigInteger('department_id')->nullable();

            // ── Core Data ─────────────────────────────────────────────
            $table->string('name', 150);
            $table->string('code', 50);
            $table->text('description')->nullable();
            $table->string('educational_level', 50);
            $table->string('duration', 50);
            $table->unsignedSmallInteger('order')->default(0);

            // ── Status ────────────────────────────────────────────────
            $table->string('status', 20)->default('active');

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
            $table->unique(['school_id', 'name']);
            $table->unique(['school_id', 'code'], 'programs_school_code_unique');
            $table->index('status');
            $table->index(['school_id', 'order']);
            $table->index('department_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
