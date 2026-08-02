<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('streams', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id')->nullable()->constrained('tenants')->cascadeOnDelete();

            $table->foreignId('academic_program_id')->constrained('academic_programs')->cascadeOnDelete();
            $table->foreignId('education_level_id')->nullable()->constrained('education_levels')->restrictOnDelete();
            $table->string('name', 100);
            $table->string('code', 50)->nullable();
            $table->text('description')->nullable();
            $table->string('status', 20)->default('active');

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['school_id', 'academic_program_id', 'name']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('streams');
    }
};
