<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('council_sessions', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->unsignedBigInteger('academic_calendar_year_id');
            $table->unsignedBigInteger('academic_calendar_term_id');
            $table->unsignedBigInteger('school_class_id');
            $table->unsignedBigInteger('class_section_id')->nullable();

            $table->string('reference', 40);
            $table->string('status', 30)->default('draft');

            $table->timestamp('held_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('archived_at')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('academic_calendar_year_id')->references('id')->on('academic_calendar_years')->cascadeOnDelete();
            $table->foreign('academic_calendar_term_id')->references('id')->on('academic_calendar_terms')->cascadeOnDelete();
            $table->foreign('school_class_id')->references('id')->on('school_classes')->cascadeOnDelete();
            $table->foreign('class_section_id')->references('id')->on('class_sections')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();

            $table->unique(['school_id', 'reference'], 'council_sessions_reference_unique');
            $table->index(['school_id', 'status']);
            $table->index(['school_id', 'academic_calendar_year_id', 'academic_calendar_term_id', 'school_class_id'], 'council_sessions_school_year_term_class_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('council_sessions');
    }
};
