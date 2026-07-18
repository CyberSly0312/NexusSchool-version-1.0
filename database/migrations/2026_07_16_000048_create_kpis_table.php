<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('school_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name', 255);
            $table->string('slug', 255);
            $table->text('description')->nullable();
            $table->string('category', 100);
            $table->json('formula')->nullable();
            $table->decimal('target_value', 15, 2)->nullable();
            $table->string('unit', 50)->nullable();
            $table->string('status', 20);
            $table->string('calculation_frequency', 36)->nullable();
            $table->timestamp('last_calculated_at')->nullable();
            $table->decimal('cached_value', 15, 2)->nullable();
            $table->timestamp('cached_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('school_id');
            $table->index('category');
            $table->index('status');
            $table->unique(['school_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpis');
    }
};
