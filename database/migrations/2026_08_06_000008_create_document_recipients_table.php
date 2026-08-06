<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('document_recipients', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->unsignedBigInteger('academic_document_id');
            $table->unsignedBigInteger('document_version_id');
            $table->string('recipient_type', 120);
            $table->unsignedBigInteger('recipient_id')->nullable();
            $table->string('recipient_label');
            $table->string('visibility_scope', 30);

            $table->timestamp('portal_viewed_at')->nullable();
            $table->unsignedInteger('portal_view_count')->default(0);
            $table->timestamp('delivered_at')->nullable();

            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('academic_document_id')->references('id')->on('academic_documents')->cascadeOnDelete();
            $table->foreign('document_version_id')->references('id')->on('academic_document_versions')->cascadeOnDelete();

            $table->index(['school_id', 'academic_document_id']);
            $table->index(['school_id', 'recipient_type', 'recipient_id']);
            $table->index(['school_id', 'visibility_scope']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_recipients');
    }
};
