<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('school_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('ai_provider_id')->nullable()->constrained('ai_providers')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title', 255)->nullable();
            $table->string('status', 36)->default('active');
            $table->string('context_type', 100)->nullable();
            $table->string('context_id', 36)->nullable();
            $table->string('model', 255)->nullable();
            $table->text('system_prompt')->nullable();
            $table->text('summary')->nullable();
            $table->unsignedInteger('message_count')->default(0);
            $table->unsignedBigInteger('total_input_tokens')->default(0);
            $table->unsignedBigInteger('total_output_tokens')->default(0);
            $table->unsignedInteger('context_window_used')->default(0);
            $table->unsignedInteger('context_window_limit')->default(128000);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('school_id');
            $table->index('user_id');
            $table->index('ai_provider_id');
            $table->index('status');
            $table->index('context_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_conversations');
    }
};
