<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('api_client_id');
            $table->foreign('api_client_id')->references('id')->on('api_clients')->cascadeOnDelete();
            $table->foreignId('school_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name', 255);
            $table->string('token', 255)->unique();
            $table->json('abilities')->nullable();
            $table->datetime('expires_at')->nullable();
            $table->datetime('last_used_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('api_client_id');
            $table->index('school_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_tokens');
    }
};
