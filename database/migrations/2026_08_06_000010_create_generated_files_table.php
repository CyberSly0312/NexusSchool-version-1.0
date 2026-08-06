<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('generated_files', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->unsignedBigInteger('academic_document_id');
            $table->unsignedBigInteger('document_version_id');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('disk', 40)->default('local');
            $table->string('mime_type', 100)->default('application/pdf');
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->string('checksum', 64);
            $table->boolean('is_current')->default(true);

            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('academic_document_id')->references('id')->on('academic_documents')->cascadeOnDelete();
            $table->foreign('document_version_id')->references('id')->on('academic_document_versions')->cascadeOnDelete();

            $table->index(['school_id', 'academic_document_id', 'is_current']);
            $table->index(['school_id', 'document_version_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_files');
    }
};
