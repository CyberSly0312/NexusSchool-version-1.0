<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('completion_request_entries', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->unsignedBigInteger('completion_request_id');

            $table->json('content')->nullable();

            $table->unsignedBigInteger('academic_document_id')->nullable();
            $table->string('academic_document_number', 64)->nullable();

            $table->string('academic_document_type', 30)->nullable();

            $table->string('status', 30)->default('pending');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('completion_request_id')->references('id')->on('completion_requests')->cascadeOnDelete();
            $table->foreign('academic_document_id')->references('id')->on('academic_documents')->nullOnDelete();

            $table->index(['school_id', 'status']);
            $table->unique(['completion_request_id'], 'completion_request_entries_request_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('completion_request_entries');
    }
};
