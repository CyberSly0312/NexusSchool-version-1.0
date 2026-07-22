<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_knowledge_sources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('school_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name', 255);
            $table->string('slug', 255);
            $table->string('type', 50);
            $table->text('description')->nullable();
            $table->string('status', 36)->default('pending');
            $table->string('source_path', 500)->nullable();
            $table->string('source_type', 100)->nullable();
            $table->string('content_type', 100)->nullable();
            $table->unsignedBigInteger('content_size')->nullable();
            $table->string('content_hash', 64)->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->unsignedInteger('chunk_count')->default(0);
            $table->unsignedBigInteger('total_tokens')->default(0);
            $table->json('configuration')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('indexed_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('school_id');
            $table->index('type');
            $table->index('status');
            $table->unique(['school_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_knowledge_sources');
    }
};
