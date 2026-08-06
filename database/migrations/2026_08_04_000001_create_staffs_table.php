<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('staffs', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            // ── Staff reference ───────────────────────────────────────
            $table->string('staff_number', 50);

            // ── Personal information ──────────────────────────────────
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('preferred_name', 100)->nullable();
            $table->string('gender', 20);
            $table->date('date_of_birth')->nullable();
            $table->string('nationality', 100)->nullable();
            $table->string('photo', 255)->nullable();

            // ── Contact ───────────────────────────────────────────────
            $table->string('email', 255)->nullable();
            $table->string('phone_number', 30);
            $table->string('alternate_phone', 30)->nullable();
            $table->text('residential_address')->nullable();

            // ── Legal & financial identifiers ─────────────────────────
            $table->string('national_id', 50)->nullable();
            $table->string('tax_number', 50)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account', 50)->nullable();

            // ── Specialization (Teacher is a specialization of Staff) ─
            $table->string('specialization', 30)->default('other');

            // ── User link (optional) ──────────────────────────────────
            $table->unsignedBigInteger('user_id')->nullable();

            // ── Status & notes ────────────────────────────────────────
            $table->string('status', 20)->default('active');
            $table->text('notes')->nullable();

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->unique(['school_id', 'staff_number']);
            $table->unique(['school_id', 'email'])->whereNotNull('email');
            $table->unique(['school_id', 'phone_number']);
            $table->index('status');
            $table->index(['school_id', 'status']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staffs');
    }
};
