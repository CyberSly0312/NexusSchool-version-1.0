<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('publication_records', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->unsignedBigInteger('academic_document_id');
            $table->unsignedBigInteger('document_version_id');
            $table->string('channel', 40);
            $table->string('recipient_type', 120);
            $table->unsignedBigInteger('recipient_id')->nullable();
            $table->string('recipient_label');
            $table->string('visibility_scope', 30);

            $table->string('status', 30)->default('queued');
            $table->unsignedInteger('attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('academic_document_id')->references('id')->on('academic_documents')->cascadeOnDelete();
            $table->foreign('document_version_id')->references('id')->on('academic_document_versions')->cascadeOnDelete();

            $table->index(['school_id', 'status']);
            $table->index(['school_id', 'academic_document_id', 'status']);
            $table->index(['school_id', 'channel', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publication_records');
    }
};
