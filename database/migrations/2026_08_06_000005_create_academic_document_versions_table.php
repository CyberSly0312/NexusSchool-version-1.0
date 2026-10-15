<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('academic_document_versions', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->unsignedBigInteger('academic_document_id');
            $table->unsignedInteger('version_number');
            $table->string('status', 30)->default('draft');
            $table->json('content');
            $table->string('content_hash', 64);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('academic_document_id')->references('id')->on('academic_documents')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();

            $table->unique(['academic_document_id', 'version_number'], 'academic_document_versions_doc_version_unique');
            $table->index(['school_id', 'academic_document_id']);
            $table->index(['school_id', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_document_versions');
    }
};
