<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            // ── Book Identifiers ──────────────────────────────────────
            $table->string('isbn', 20)->nullable();
            $table->string('title', 255);
            $table->string('subtitle', 255)->nullable();

            // ── Book Details ──────────────────────────────────────────
            $table->text('description')->nullable();
            $table->string('language', 50)->default('English');
            $table->string('edition', 50)->nullable();
            $table->smallInteger('publication_year')->nullable();

            // ── Relationships ─────────────────────────────────────────
            $table->unsignedBigInteger('publisher_id')->nullable();
            $table->unsignedBigInteger('book_category_id')->nullable();

            // ── Library Location ──────────────────────────────────────
            $table->string('shelf_location', 100)->nullable();
            $table->string('cover_image', 255)->nullable();

            // ── Status ────────────────────────────────────────────────
            $table->string('status', 20)->default('active');

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('publisher_id')->references('id')->on('publishers')->nullOnDelete();
            $table->foreign('book_category_id')->references('id')->on('book_categories')->nullOnDelete();
            $table->unique(['school_id', 'isbn']);
            $table->index('status');
            $table->index(['school_id', 'status']);
            $table->index('publisher_id');
            $table->index('book_category_id');
            $table->index('title');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
