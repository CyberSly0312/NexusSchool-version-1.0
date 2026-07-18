<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_health', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('service', 100);
            $table->string('status', 30)->default('unknown');
            $table->unsignedSmallInteger('response_time_ms')->nullable();
            $table->text('message')->nullable();
            $table->json('metadata')->nullable();

            $table->unsignedSmallInteger('cpu_usage_percent')->nullable();
            $table->unsignedSmallInteger('memory_usage_percent')->nullable();
            $table->unsignedBigInteger('disk_used_bytes')->nullable();
            $table->unsignedBigInteger('disk_total_bytes')->nullable();

            $table->timestamp('checked_at')->useCurrent();

            $table->timestamps();

            $table->index(['service', 'checked_at']);
            $table->index('status');
            $table->index('checked_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_health');
    }
};
