<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('assessment_scores', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->unsignedBigInteger('assessment_id');
            $table->unsignedBigInteger('assessment_component_id')->nullable();
            $table->unsignedBigInteger('enrollment_id');
            $table->decimal('score', 8, 2);
            $table->decimal('percentage', 8, 2)->nullable();
            $table->string('status', 30)->default('draft');
            $table->string('validation_status', 30)->default('draft');
            $table->text('feedback')->nullable();

            $table->unsignedBigInteger('graded_by')->nullable();
            $table->timestamp('graded_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('assessment_id')->references('id')->on('assessments')->cascadeOnDelete();
            $table->foreign('assessment_component_id')->references('id')->on('assessment_components')->cascadeOnDelete();
            $table->foreign('enrollment_id')->references('id')->on('enrollments')->cascadeOnDelete();
            $table->foreign('graded_by')->references('id')->on('users')->nullOnDelete();

            $table->unique(['assessment_id', 'assessment_component_id', 'enrollment_id'], 'assessment_scores_enrollment_unique');
            $table->index(['school_id', 'status']);
            $table->index(['school_id', 'enrollment_id']);
            $table->index(['assessment_id', 'validation_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_scores');
    }
};
