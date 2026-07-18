<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_deliveries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('webhook_id');
            $table->foreign('webhook_id')->references('id')->on('webhooks')->cascadeOnDelete();
            $table->foreignId('school_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('event', 255);
            $table->json('payload');
            $table->integer('response_status')->nullable();
            $table->text('response_body')->nullable();
            $table->boolean('success')->default(false);
            $table->datetime('delivered_at')->nullable();
            $table->integer('retry_count')->default(0);
            $table->integer('max_retries')->default(3);
            $table->datetime('next_retry_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('webhook_id');
            $table->index('school_id');
            $table->index('event');
            $table->index('success');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_deliveries');
    }
};
