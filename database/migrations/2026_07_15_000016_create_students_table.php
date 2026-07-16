<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->id();
            $table->ulid('uuid')->unique();
            $table->string('school_id', 36);

            // ── Student Identifiers ───────────────────────────────────
            $table->string('student_number', 50);
            $table->string('admission_number', 50)->nullable();
            $table->string('national_id', 100)->nullable();

            // ── Personal Information ──────────────────────────────────
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('preferred_name', 100)->nullable();
            $table->string('gender', 20);
            $table->date('date_of_birth');
            $table->string('place_of_birth', 200)->nullable();
            $table->string('nationality', 100)->nullable();
            $table->string('blood_group', 10)->nullable();
            $table->string('religion', 100)->nullable();
            $table->string('photo', 255)->nullable();

            // ── Contact Information ──────────────────────────────────
            $table->string('email', 255)->nullable();
            $table->string('phone_number', 30)->nullable();
            $table->text('residential_address')->nullable();

            // ── Academic Information ─────────────────────────────────
            $table->date('admission_date')->nullable();

            // ── Medical Information ───────────────────────────────────
            $table->text('allergies')->nullable();
            $table->text('medical_notes')->nullable();

            // ── User Link (optional) ─────────────────────────────────
            $table->unsignedBigInteger('user_id')->nullable();

            // ── Additional ────────────────────────────────────────────
            $table->string('notes')->nullable();

            // ── Status ────────────────────────────────────────────────
            $table->string('status', 20)->default('active');

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->unique(['school_id', 'student_number']);
            $table->unique(['school_id', 'admission_number'])->whereNotNull('admission_number');
            $table->index('status');
            $table->index(['school_id', 'status']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
