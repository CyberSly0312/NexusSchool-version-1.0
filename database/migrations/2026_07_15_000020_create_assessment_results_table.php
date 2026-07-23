<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_results', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('student_enrollment_id')->nullable();
            $table->unsignedBigInteger('assessment_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('academic_year_id');
            $table->unsignedBigInteger('academic_term_id');

            $table->decimal('raw_score', 8, 2);
            $table->decimal('adjusted_score', 8, 2)->nullable();
            $table->decimal('maximum_score', 8, 2);
            $table->decimal('percentage', 8, 2)->nullable();
            $table->string('letter_grade', 5)->nullable();
            $table->decimal('grade_point', 4, 2)->nullable();
            $table->decimal('coefficient', 5, 2)->default(1.00);
            $table->decimal('weighted_score', 12, 4)->nullable();
            $table->unsignedInteger('rank_in_assessment')->nullable();
            $table->boolean('is_absent')->default(false);
            $table->boolean('is_excused')->default(false);
            $table->boolean('is_published')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->string('status', 20)->default('draft');
            $table->text('remarks')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->foreign('student_enrollment_id')->references('id')->on('student_enrollments')->nullOnDelete();
            $table->foreign('assessment_id')->references('id')->on('assessments')->cascadeOnDelete();
            $table->foreign('subject_id')->references('id')->on('subjects')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
            $table->foreign('academic_term_id')->references('id')->on('academic_terms')->cascadeOnDelete();

            $table->unique(['student_id', 'assessment_id'], 'unique_student_assessment');
            $table->index('school_id');
            $table->index('student_enrollment_id');
            $table->index('status');
            $table->index(['assessment_id', 'status']);
            $table->index(['subject_id', 'academic_term_id']);
            $table->index(['academic_year_id', 'academic_term_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_results');
    }
};
