<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('assessment_weights', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->unsignedBigInteger('assessment_type_id');
            $table->unsignedBigInteger('academic_subject_id')->nullable();
            $table->unsignedBigInteger('academic_calendar_year_id')->nullable();
            $table->unsignedBigInteger('academic_calendar_term_id')->nullable();

            $table->decimal('weight', 5, 2);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->date('effective_from')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('assessment_type_id')->references('id')->on('assessment_types')->restrictOnDelete();
            $table->foreign('academic_subject_id')->references('id')->on('academic_subjects')->cascadeOnDelete();
            $table->foreign('academic_calendar_year_id')->references('id')->on('academic_calendar_years')->cascadeOnDelete();
            $table->foreign('academic_calendar_term_id')->references('id')->on('academic_calendar_terms')->cascadeOnDelete();

            $table->index(['school_id', 'assessment_type_id']);
            $table->index(['school_id', 'academic_subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_weights');
    }
};
