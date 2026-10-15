<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('employment_history', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            // ── References ────────────────────────────────────────────
            $table->foreignId('employment_id')->constrained('employments')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staffs')->cascadeOnDelete();

            // ── Event ─────────────────────────────────────────────────
            $table->string('event', 50);
            $table->string('description', 255)->nullable();
            $table->json('payload')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_type', 255)->nullable();
            $table->dateTime('occurred_at');

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->index(['employment_id', 'occurred_at']);
            $table->index(['school_id', 'employment_id']);
            $table->index(['staff_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employment_history');
    }
};
