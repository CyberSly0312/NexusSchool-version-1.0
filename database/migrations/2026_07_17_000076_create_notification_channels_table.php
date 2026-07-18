<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_channels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('school_id')->nullable();

            $table->string('name', 200);
            $table->string('type', 50);
            $table->boolean('is_active')->default(true);
            $table->json('configuration')->nullable();
            $table->json('conditions')->nullable();
            $table->text('description')->nullable();

            $table->unsignedInteger('total_sent')->default(0);
            $table->unsignedInteger('total_failed')->default(0);
            $table->timestamp('last_used_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'type']);
            $table->index(['school_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_channels');
    }
};
