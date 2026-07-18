<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('school_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name', 255);
            $table->string('slug', 255);
            $table->text('description')->nullable();
            $table->string('type', 100);
            $table->string('format', 10);
            $table->string('status', 20);
            $table->json('config')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->string('scheduled_frequency', 36)->nullable();
            $table->timestamp('last_generated_at')->nullable();
            $table->unsignedBigInteger('generated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('school_id');
            $table->index('status');
            $table->index('scheduled_at');
            $table->unique(['school_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
