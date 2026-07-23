<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('database')->unique();
            $table->string('domain')->unique()->nullable();

            // ── Contact ───────────────────────────────────────────────
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();

            // ── Configuration ─────────────────────────────────────────
            $table->string('timezone')->default('UTC');
            $table->string('currency', 10)->default('USD');
            $table->string('locale', 10)->default('en');
            $table->string('logo_url')->nullable();
            $table->string('registration_number')->nullable()->unique();
            $table->string('school_type')->default('secondary');

            // ── Status ────────────────────────────────────────────────
            $table->string('status')->default('pending');

            // ── Flexible Data ─────────────────────────────────────────
            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Indexes ───────────────────────────────────────────────
            $table->index('status');
            $table->index('country');
            $table->index('school_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
