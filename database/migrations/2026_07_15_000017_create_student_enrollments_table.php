<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id');
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained();
            $table->foreignId('academic_class_id')->constrained();
            $table->foreignId('program_id')->constrained();
            $table->foreignId('grade_level_id')->constrained();
            $table->foreignId('section_id')->constrained();
            $table->foreignId('classroom_id')->nullable()->constrained();
            $table->foreignId('academic_term_id')->nullable()->constrained();
            $table->string('enrollment_number');
            $table->date('enrollment_date');
            $table->date('effective_date');
            $table->string('enrollment_type');
            $table->string('status');
            $table->string('previous_school')->nullable();
            $table->string('transfer_reason')->nullable();
            $table->date('exit_date')->nullable();
            $table->string('exit_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['school_id', 'enrollment_number']);
            $table->index('student_id');
            $table->index('academic_year_id');
            $table->index('academic_class_id');
            $table->index('program_id');
            $table->index('grade_level_id');
            $table->index('section_id');
            $table->index(['student_id', 'academic_year_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');
    }
};
