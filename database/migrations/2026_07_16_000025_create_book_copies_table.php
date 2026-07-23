<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_copies', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);
            $table->unsignedBigInteger('book_id');

            // ── Copy Identifiers ──────────────────────────────────────
            $table->string('barcode', 50);
            $table->string('inventory_number', 50);

            // ── Acquisition ───────────────────────────────────────────
            $table->date('acquisition_date')->nullable();
            $table->decimal('purchase_cost', 10, 2)->nullable();

            // ── Condition & Status ────────────────────────────────────
            $table->string('condition', 20)->default('good');
            $table->boolean('is_available')->default(true);
            $table->string('status', 20)->default('available');

            // ── Notes ─────────────────────────────────────────────────
            $table->text('notes')->nullable();

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('book_id')->references('id')->on('books')->cascadeOnDelete();
            $table->unique(['school_id', 'barcode']);
            $table->unique(['school_id', 'inventory_number']);
            $table->index('status');
            $table->index(['school_id', 'status']);
            $table->index(['book_id', 'is_available']);
            $table->index('is_available');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_copies');
    }
};
