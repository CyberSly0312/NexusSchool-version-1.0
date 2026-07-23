<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_loans', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            // ── Loan Details ──────────────────────────────────────────
            $table->unsignedBigInteger('book_copy_id');
            $table->unsignedBigInteger('borrower_id');
            $table->string('borrower_type', 20)->default('student');

            // ── Dates ─────────────────────────────────────────────────
            $table->date('loan_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();

            // ── Status ────────────────────────────────────────────────
            $table->string('status', 20)->default('active');
            $table->integer('renewal_count')->default(0);

            // ── Staff ─────────────────────────────────────────────────
            $table->unsignedBigInteger('issued_by')->nullable();
            $table->unsignedBigInteger('returned_to')->nullable();

            // ── Notes ─────────────────────────────────────────────────
            $table->text('notes')->nullable();

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('book_copy_id')->references('id')->on('book_copies')->cascadeOnDelete();
            $table->index('status');
            $table->index(['school_id', 'status']);
            $table->index(['borrower_id', 'borrower_type']);
            $table->index('book_copy_id');
            $table->index('due_date');
            $table->index(['status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_loans');
    }
};
