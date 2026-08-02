<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coefficients', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id')->nullable()->constrained('tenants')->cascadeOnDelete();

            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('academic_subject_id')->constrained('academic_subjects')->cascadeOnDelete();
            $table->decimal('coefficient', 5, 2)->default(1.00);
            $table->boolean('is_optional')->default(false);
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['school_id', 'school_class_id', 'academic_subject_id']);
            $table->index(['school_id', 'academic_subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coefficients');
    }
};
