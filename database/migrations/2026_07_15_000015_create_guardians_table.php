<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guardians', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->id();
            $table->ulid('uuid')->unique();
            $table->string('school_id', 36);

            // ── Personal Information ──────────────────────────────────
            $table->string('guardian_number', 50);
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('preferred_name', 100)->nullable();
            $table->string('gender', 20);
            $table->date('date_of_birth')->nullable();
            $table->string('nationality', 100)->nullable();
            $table->string('relationship_type', 30);
            $table->string('email', 255)->nullable();
            $table->string('phone_number', 30);
            $table->string('alternate_phone', 30)->nullable();
            $table->string('occupation', 200)->nullable();
            $table->string('employer', 200)->nullable();
            $table->text('residential_address')->nullable();
            $table->text('postal_address')->nullable();
            $table->text('emergency_contact')->nullable();
            $table->string('preferred_communication_language', 50)->nullable();
            $table->string('preferred_contact_method', 30)->default('phone');
            $table->boolean('is_primary_guardian')->default(false);
            $table->string('notes')->nullable();

            // ── User Link (optional) ─────────────────────────────────
            $table->unsignedBigInteger('user_id')->nullable();

            // ── Status ────────────────────────────────────────────────
            $table->string('status', 20)->default('active');

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->unique(['school_id', 'guardian_number']);
            $table->unique(['school_id', 'phone_number']);
            $table->unique(['school_id', 'email'])->whereNotNull('email');
            $table->index('status');
            $table->index(['school_id', 'status']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};
