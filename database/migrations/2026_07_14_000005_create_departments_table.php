<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id');

            // ── Core Data ─────────────────────────────────────────────
            $table->string('name', 150);
            $table->string('code', 50);
            $table->text('description')->nullable();
            $table->string('color', 20)->nullable();
            $table->unsignedSmallInteger('order')->default(0);
            $table->unsignedBigInteger('head_id')->nullable();

            // ── Status ────────────────────────────────────────────────
            $table->string('status', 20)->default('active');

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->unique(['school_id', 'name']);
            $table->unique(['school_id', 'code'], 'departments_school_code_unique');
            $table->index('status');
            $table->index(['school_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
