<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_providers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('school_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name', 255);
            $table->string('slug', 255);
            $table->string('driver', 100);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('configuration')->nullable();
            $table->json('supported_models')->nullable();
            $table->string('default_model', 255)->nullable();
            $table->integer('rate_limit_per_minute')->default(60);
            $table->integer('daily_token_limit')->nullable();
            $table->integer('monthly_token_limit')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('school_id');
            $table->index('driver');
            $table->index('is_active');
            $table->unique(['school_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_providers');
    }
};
