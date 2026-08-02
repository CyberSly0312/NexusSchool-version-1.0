<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_programs', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id')->nullable()->constrained('tenants')->cascadeOnDelete();

            $table->foreignId('education_level_id')->nullable()->constrained('education_levels')->nullOnDelete();
            $table->string('name', 100);
            $table->string('code', 50)->nullable();
            $table->text('description')->nullable();
            $table->string('status', 20)->default('active');

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['school_id', 'name']);
            $table->index('status');
            $table->index(['school_id', 'education_level_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_programs');
    }
};
