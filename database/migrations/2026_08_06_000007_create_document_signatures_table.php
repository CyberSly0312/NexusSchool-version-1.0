<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('document_signatures', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->unsignedBigInteger('academic_document_id');
            $table->unsignedBigInteger('document_version_id');
            $table->string('signature_type', 30)->default('electronic');
            $table->string('role', 60);
            $table->string('signatory_name');
            $table->unsignedBigInteger('signatory_user_id')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->string('signature_value', 512)->nullable();

            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('academic_document_id')->references('id')->on('academic_documents')->cascadeOnDelete();
            $table->foreign('document_version_id')->references('id')->on('academic_document_versions')->cascadeOnDelete();
            $table->foreign('signatory_user_id')->references('id')->on('users')->nullOnDelete();

            $table->index(['school_id', 'academic_document_id']);
            $table->index(['school_id', 'document_version_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_signatures');
    }
};
