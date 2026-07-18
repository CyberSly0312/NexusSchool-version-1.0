<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('school_id')->nullable();

            $table->string('log_name', 100)->nullable();
            $table->string('description');
            $table->string('subject_type', 100)->nullable();
            $table->string('subject_id', 36)->nullable();
            $table->string('event', 100)->nullable();
            $table->json('properties')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();

            $table->string('causer_id', 36)->nullable();
            $table->string('causer_type', 50)->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['school_id', 'created_at']);
            $table->index(['subject_type', 'subject_id']);
            $table->index('log_name');
            $table->index('event');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
