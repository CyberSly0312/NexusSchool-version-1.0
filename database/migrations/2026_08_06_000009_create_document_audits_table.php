<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('document_audits', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->unsignedBigInteger('academic_document_id')->nullable();
            $table->unsignedBigInteger('document_version_id')->nullable();
            $table->string('action', 60);
            $table->unsignedBigInteger('actor_user_id')->nullable();
            $table->unsignedBigInteger('recipient_id')->nullable();
            $table->unsignedBigInteger('publication_record_id')->nullable();
            $table->string('channel', 40)->nullable();
            $table->json('metadata')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('academic_document_id')->references('id')->on('academic_documents')->cascadeOnDelete();
            $table->foreign('document_version_id')->references('id')->on('academic_document_versions')->cascadeOnDelete();
            $table->foreign('actor_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('publication_record_id')->references('id')->on('publication_records')->nullOnDelete();

            $table->index(['school_id', 'academic_document_id']);
            $table->index(['school_id', 'action']);
            $table->index(['school_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_audits');
    }
};
