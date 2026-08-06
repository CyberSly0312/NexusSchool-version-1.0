<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('academic_document_templates', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->string('name');
            $table->string('code')->unique();
            $table->string('document_type', 50);
            $table->json('content');
            $table->longText('header_html')->nullable();
            $table->longText('footer_html')->nullable();
            $table->json('branding')->nullable();
            $table->json('typography')->nullable();
            $table->json('signature_areas')->nullable();
            $table->boolean('qr_code_enabled')->default(false);
            $table->boolean('verification_code_enabled')->default(false);
            $table->string('locale', 10)->default('fr');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->json('settings')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->index(['school_id', 'document_type']);
            $table->index(['school_id', 'is_active', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_document_templates');
    }
};
