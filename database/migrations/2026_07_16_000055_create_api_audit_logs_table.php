<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('api_client_id')->nullable();
            $table->foreign('api_client_id')->references('id')->on('api_clients')->nullOnDelete();
            $table->string('school_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->uuid('api_token_id')->nullable();
            $table->string('method', 10);
            $table->string('path', 500);
            $table->integer('status_code')->nullable();
            $table->string('ip_address', 45);
            $table->string('user_agent', 500)->nullable();
            $table->json('request_body')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->timestamps();

            $table->index('api_client_id');
            $table->index('school_id');
            $table->index('user_id');
            $table->index('method');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_audit_logs');
    }
};
