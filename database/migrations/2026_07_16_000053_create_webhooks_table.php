<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhooks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('api_client_id');
            $table->foreign('api_client_id')->references('id')->on('api_clients')->cascadeOnDelete();
            $table->string('school_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('url', 500);
            $table->string('secret', 255);
            $table->json('events');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->integer('failure_count')->default(0);
            $table->datetime('last_triggered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('api_client_id');
            $table->index('school_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhooks');
    }
};
