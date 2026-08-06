<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('document_approvals', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->unsignedBigInteger('academic_document_id');
            $table->unsignedBigInteger('document_version_id');
            $table->string('approver_role', 60);
            $table->unsignedBigInteger('approver_user_id')->nullable();
            $table->string('status', 30)->default('pending');
            $table->text('comment')->nullable();
            $table->timestamp('decided_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('academic_document_id')->references('id')->on('academic_documents')->cascadeOnDelete();
            $table->foreign('document_version_id')->references('id')->on('academic_document_versions')->cascadeOnDelete();
            $table->foreign('approver_user_id')->references('id')->on('users')->nullOnDelete();

            $table->index(['school_id', 'academic_document_id', 'status']);
            $table->index(['school_id', 'document_version_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_approvals');
    }
};
