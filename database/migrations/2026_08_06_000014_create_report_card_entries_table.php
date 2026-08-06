<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('report_card_entries', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->unsignedBigInteger('report_card_id');
            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('student_id');

            $table->json('content')->nullable();

            $table->unsignedBigInteger('academic_document_id')->nullable();
            $table->string('academic_document_number', 64)->nullable();

            $table->string('status', 30)->default('pending');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('report_card_id')->references('id')->on('report_cards')->cascadeOnDelete();
            $table->foreign('enrollment_id')->references('id')->on('enrollments')->cascadeOnDelete();
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->foreign('academic_document_id')->references('id')->on('academic_documents')->nullOnDelete();

            $table->unique(['report_card_id', 'enrollment_id'], 'report_card_entries_run_student_unique');
            $table->index(['school_id', 'status']);
            $table->index(['school_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_card_entries');
    }
};
