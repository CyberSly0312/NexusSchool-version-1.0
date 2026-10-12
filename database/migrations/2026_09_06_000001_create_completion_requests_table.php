<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('completion_requests', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->unsignedBigInteger('student_id');
            $table->string('completion_document_kind', 30);
            $table->string('status', 30)->default('draft');

            $table->string('eligibility_verdict', 30)->nullable();
            $table->json('eligibility_reasons')->nullable();
            $table->json('evidence_citations')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamp('generated_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('archived_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();

            $table->index(['school_id', 'status']);
            $table->index(['school_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('completion_requests');
    }
};
