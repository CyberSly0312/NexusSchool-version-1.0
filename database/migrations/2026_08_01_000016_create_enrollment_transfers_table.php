<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('enrollment_transfers', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id');
            $table->foreignId('enrollment_id')->constrained('enrollments')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('transfer_type');
            $table->foreignId('from_class_id')->nullable()->constrained('school_classes')->restrictOnDelete();
            $table->foreignId('to_class_id')->nullable()->constrained('school_classes')->restrictOnDelete();
            $table->foreignId('from_section_id')->nullable()->constrained('class_sections')->nullOnDelete();
            $table->foreignId('to_section_id')->nullable()->constrained('class_sections')->nullOnDelete();
            $table->string('from_school_id', 50)->nullable();
            $table->string('to_school_id', 50)->nullable();
            $table->string('reason', 255)->nullable();
            $table->date('transfer_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'student_id']);
            $table->index(['school_id', 'enrollment_id']);
            $table->index(['school_id', 'transfer_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_transfers');
    }
};
