<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('grade_entries', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->unsignedBigInteger('gradebook_id');
            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('student_id');

            $table->decimal('percentage', 8, 2)->nullable();
            $table->decimal('average', 8, 2)->nullable();
            $table->string('letter_grade', 8)->nullable();
            $table->decimal('grade_point', 4, 2)->nullable();
            $table->string('appreciation', 60)->nullable();

            $table->unsignedInteger('rank')->nullable();
            $table->unsignedInteger('rank_denominator')->nullable();

            $table->string('status', 30)->default('draft');
            $table->text('remarks')->nullable();

            $table->unsignedBigInteger('graded_by')->nullable();
            $table->timestamp('calculated_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('gradebook_id')->references('id')->on('gradebooks')->cascadeOnDelete();
            $table->foreign('enrollment_id')->references('id')->on('enrollments')->cascadeOnDelete();
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->foreign('graded_by')->references('id')->on('users')->nullOnDelete();

            $table->unique(['gradebook_id', 'enrollment_id'], 'grade_entries_gradebook_enrollment_unique');
            $table->index(['school_id', 'status']);
            $table->index(['school_id', 'student_id']);
            $table->index(['gradebook_id', 'percentage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_entries');
    }
};
