<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('enrollment_graduations', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id');
            $table->foreignId('enrollment_id')->constrained('enrollments')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('education_level_id')->constrained('education_levels')->restrictOnDelete();
            $table->string('decision');
            $table->date('graduated_on');
            $table->string('certificate_reference', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'student_id']);
            $table->index(['school_id', 'enrollment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_graduations');
    }
};
