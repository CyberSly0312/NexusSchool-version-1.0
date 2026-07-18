<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_clients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('school_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name', 255);
            $table->string('slug', 255);
            $table->text('description')->nullable();
            $table->string('secret', 255);
            $table->string('callback_url', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('scopes')->nullable();
            $table->integer('rate_limit_per_minute')->default(60);
            $table->integer('rate_limit_per_day')->default(10000);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('school_id');
            $table->unique(['school_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_clients');
    }
};
