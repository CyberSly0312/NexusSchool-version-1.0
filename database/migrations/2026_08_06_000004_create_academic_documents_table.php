<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('academic_documents', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->string('document_number', 60)->unique();
            $table->string('document_type', 50);
            $table->string('title');
            $table->string('status', 30)->default('draft');
            $table->string('approval_status', 30)->default('not_required');
            $table->boolean('approval_required')->default(false);
            $table->string('origin', 30)->default('manual');

            $table->unsignedBigInteger('academic_calendar_year_id')->nullable();
            $table->unsignedBigInteger('academic_calendar_term_id')->nullable();
            $table->unsignedBigInteger('school_class_id')->nullable();
            $table->unsignedBigInteger('class_section_id')->nullable();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('staff_id')->nullable();
            $table->unsignedBigInteger('council_session_id')->nullable();

            $table->unsignedBigInteger('academic_document_template_id')->nullable();
            $table->unsignedBigInteger('publication_policy_id')->nullable();

            $table->json('content')->nullable();
            $table->unsignedInteger('content_version')->default(1);
            $table->json('visibility_policy')->nullable();

            $table->unsignedBigInteger('generated_by')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('academic_calendar_year_id')->references('id')->on('academic_calendar_years')->nullOnDelete();
            $table->foreign('academic_calendar_term_id')->references('id')->on('academic_calendar_terms')->nullOnDelete();
            $table->foreign('school_class_id')->references('id')->on('school_classes')->nullOnDelete();
            $table->foreign('class_section_id')->references('id')->on('class_sections')->nullOnDelete();
            $table->foreign('academic_document_template_id')->references('id')->on('academic_document_templates')->nullOnDelete();
            $table->foreign('publication_policy_id')->references('id')->on('publication_policies')->nullOnDelete();

            $table->index(['school_id', 'status']);
            $table->index(['school_id', 'document_type']);
            $table->index(['school_id', 'academic_calendar_year_id', 'academic_calendar_term_id'], 'academic_documents_school_year_term_index');
            $table->index(['school_id', 'student_id']);
            $table->index(['school_id', 'staff_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_documents');
    }
};
