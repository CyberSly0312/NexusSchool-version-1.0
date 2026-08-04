<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('enrollment_promotions', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id');
            $table->foreignId('enrollment_id')->constrained('enrollments')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_calendar_year_id')->constrained('academic_calendar_years')->restrictOnDelete();
            $table->foreignId('from_class_id')->constrained('school_classes')->restrictOnDelete();
            $table->foreignId('to_class_id')->nullable()->constrained('school_classes')->restrictOnDelete();
            $table->string('decision');
            $table->date('promoted_on');
            $table->json('academic_result')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'student_id']);
            $table->index(['school_id', 'enrollment_id']);
            $table->index(['school_id', 'academic_calendar_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_promotions');
    }
};
