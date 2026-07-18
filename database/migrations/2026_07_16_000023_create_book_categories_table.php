<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_categories', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique();
            $table->string('school_id', 36);

            // ── Category Information ──────────────────────────────────
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();

            // ── Status ────────────────────────────────────────────────
            $table->string('status', 20)->default('active');

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('parent_id')->references('id')->on('book_categories')->nullOnDelete();
            $table->unique(['school_id', 'name']);
            $table->index('status');
            $table->index(['school_id', 'status']);
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_categories');
    }
};
