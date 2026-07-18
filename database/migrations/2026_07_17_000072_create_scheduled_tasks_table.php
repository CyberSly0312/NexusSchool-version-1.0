<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scheduled_tasks', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique();
            $table->string('school_id')->nullable();

            $table->string('name', 200);
            $table->string('command', 255);
            $table->text('arguments')->nullable();
            $table->string('cron_expression', 100);
            $table->string('timezone', 50)->default('UTC');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_singleton')->default(false);

            $table->string('status', 30)->default('pending');
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->unsignedInteger('total_runs')->default(0);
            $table->unsignedInteger('failed_runs')->default(0);
            $table->text('last_error')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'next_run_at']);
            $table->index('command');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_tasks');
    }
};
