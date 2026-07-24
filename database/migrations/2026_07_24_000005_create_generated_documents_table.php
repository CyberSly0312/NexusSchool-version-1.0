<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('school_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('template_id')->nullable()->constrained('document_templates')->nullOnDelete();
            $table->string('type');
            $table->string('reference_number')->unique();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->json('data')->nullable();
            $table->string('file_path')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('signed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('signed_at')->nullable();
            $table->string('qr_code')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'type']);
            $table->index(['school_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_documents');
    }
};
