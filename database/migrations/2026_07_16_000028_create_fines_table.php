<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fines', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            // ── Fine Details ──────────────────────────────────────────
            $table->unsignedBigInteger('book_loan_id')->nullable();
            $table->unsignedBigInteger('borrower_id');
            $table->string('borrower_type', 20)->default('student');
            $table->string('reason', 255);

            // ── Amounts ───────────────────────────────────────────────
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('waived_amount', 10, 2)->default(0);

            // ── Dates ─────────────────────────────────────────────────
            $table->date('fine_date');
            $table->date('paid_date')->nullable();
            $table->date('waived_date')->nullable();

            // ── Payment Details ───────────────────────────────────────
            $table->string('payment_method', 30)->nullable();
            $table->string('reference_number', 100)->nullable();

            // ── Status ────────────────────────────────────────────────
            $table->string('status', 20)->default('pending');

            // ── Staff ─────────────────────────────────────────────────
            $table->unsignedBigInteger('processed_by')->nullable();

            // ── Notes ─────────────────────────────────────────────────
            $table->text('notes')->nullable();

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('book_loan_id')->references('id')->on('book_loans')->nullOnDelete();
            $table->index('status');
            $table->index(['school_id', 'status']);
            $table->index(['borrower_id', 'borrower_type']);
            $table->index('book_loan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fines');
    }
};
