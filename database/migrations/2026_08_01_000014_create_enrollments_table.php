<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id');
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_calendar_year_id')->constrained('academic_calendar_years')->restrictOnDelete();
            $table->foreignId('academic_program_id')->constrained('academic_programs')->restrictOnDelete();
            $table->foreignId('education_level_id')->constrained('education_levels')->restrictOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->restrictOnDelete();
            $table->foreignId('class_section_id')->nullable()->constrained('class_sections')->nullOnDelete();
            $table->string('enrollment_number');
            $table->string('enrollment_type');
            $table->string('status');
            $table->date('enrolled_at')->nullable();
            $table->date('exited_at')->nullable();
            $table->string('exit_reason', 255)->nullable();
            $table->string('suspension_reason', 255)->nullable();
            $table->date('suspended_until')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['school_id', 'enrollment_number']);
            $table->index(['school_id', 'student_id']);
            $table->index(['school_id', 'academic_calendar_year_id']);
            $table->index(['school_id', 'school_class_id']);
            $table->index(['school_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
