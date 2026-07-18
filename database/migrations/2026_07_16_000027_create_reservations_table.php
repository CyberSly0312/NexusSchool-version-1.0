<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique();
            $table->string('school_id', 36);

            // ── Reservation Details ───────────────────────────────────
            $table->unsignedBigInteger('book_id');
            $table->unsignedBigInteger('borrower_id');
            $table->string('borrower_type', 20)->default('student');

            // ── Dates ─────────────────────────────────────────────────
            $table->date('reservation_date');
            $table->date('expiry_date');
            $table->date('fulfilled_date')->nullable();

            // ── Status ────────────────────────────────────────────────
            $table->string('status', 20)->default('pending');

            // ── Notes ─────────────────────────────────────────────────
            $table->text('notes')->nullable();

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('book_id')->references('id')->on('books')->cascadeOnDelete();
            $table->index('status');
            $table->index(['school_id', 'status']);
            $table->index(['borrower_id', 'borrower_type']);
            $table->index('book_id');
            $table->index('expiry_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
