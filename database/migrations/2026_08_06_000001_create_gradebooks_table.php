<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('gradebooks', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->unsignedBigInteger('academic_calendar_year_id');
            $table->unsignedBigInteger('academic_calendar_term_id');
            $table->unsignedBigInteger('academic_subject_id');
            $table->unsignedBigInteger('school_class_id');
            $table->unsignedBigInteger('class_section_id')->nullable();
            $table->unsignedBigInteger('staff_id');

            $table->decimal('coefficient', 5, 2)->default(1);
            $table->string('status', 30)->default('draft');

            $table->timestamp('calculated_at')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('archived_at')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('academic_calendar_year_id')->references('id')->on('academic_calendar_years')->restrictOnDelete();
            $table->foreign('academic_calendar_term_id')->references('id')->on('academic_calendar_terms')->restrictOnDelete();
            $table->foreign('academic_subject_id')->references('id')->on('academic_subjects')->restrictOnDelete();
            $table->foreign('school_class_id')->references('id')->on('school_classes')->restrictOnDelete();
            $table->foreign('class_section_id')->references('id')->on('class_sections')->nullOnDelete();
            $table->foreign('staff_id')->references('id')->on('staffs')->restrictOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();

            $table->index(['school_id', 'status']);
            $table->index(['school_id', 'academic_calendar_year_id', 'academic_calendar_term_id'], 'gradebooks_school_year_term_index');
            $table->index(['school_id', 'academic_subject_id']);
            $table->index(['school_id', 'school_class_id']);
            $table->index(['school_id', 'staff_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gradebooks');
    }
};
