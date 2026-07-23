<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id');
            $table->unsignedBigInteger('academic_year_id');
            $table->unsignedBigInteger('academic_term_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('academic_class_id');
            $table->string('assessment_code');
            $table->string('title');
            $table->string('assessment_type');
            $table->string('assessment_category')->default('formative');
            $table->decimal('maximum_score', 8, 2);
            $table->decimal('passing_score', 8, 2);
            $table->decimal('weight', 5, 2);
            $table->date('assessment_date')->nullable();
            $table->date('publish_date')->nullable();
            $table->date('submission_deadline')->nullable();
            $table->string('status', 20)->default('draft');
            $table->boolean('is_published')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
            $table->foreign('academic_term_id')->references('id')->on('academic_terms')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('subject_id')->references('id')->on('subjects')->cascadeOnDelete();
            $table->foreign('academic_class_id')->references('id')->on('academic_classes')->cascadeOnDelete();

            $table->unique(['school_id', 'assessment_code']);
            $table->index('academic_class_id');
            $table->index('subject_id');
            $table->index(['academic_year_id', 'academic_term_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
