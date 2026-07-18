<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_prompt_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('school_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name', 255);
            $table->string('slug', 255);
            $table->string('category', 100);
            $table->text('description')->nullable();
            $table->text('system_prompt');
            $table->text('user_prompt_template');
            $table->json('variables')->nullable();
            $table->string('status', 36)->default('active');
            $table->unsignedInteger('version')->default(1);
            $table->string('target_model', 255)->nullable();
            $table->float('temperature')->default(0.7);
            $table->unsignedInteger('max_tokens')->default(4096);
            $table->float('top_p')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('school_id');
            $table->index('category');
            $table->index('status');
            $table->unique(['school_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_prompt_templates');
    }
};
