<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subject_categories', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            // ── Core Data ─────────────────────────────────────────────
            $table->string('name', 100);
            $table->string('code', 50);
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->string('color', 20)->nullable();
            $table->string('icon', 100)->nullable();

            // ── Status ────────────────────────────────────────────────
            $table->string('status', 20)->default('active');

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->unique(['school_id', 'name']);
            $table->unique(['school_id', 'code'], 'subject_categories_school_code_unique');
            $table->unique('display_order');
            $table->index('status');
            $table->index(['school_id', 'display_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_categories');
    }
};
