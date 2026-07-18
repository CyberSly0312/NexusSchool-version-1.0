<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communication_channels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('school_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name', 255);
            $table->string('channel_type', 36);
            $table->boolean('is_active')->default(true);
            $table->json('config')->nullable();
            $table->string('from_address', 255)->nullable();
            $table->string('from_name', 255)->nullable();
            $table->timestamps();

            $table->index('school_id');
            $table->index('channel_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communication_channels');
    }
};
