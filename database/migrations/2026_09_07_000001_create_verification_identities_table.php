<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Verification identity records (SPR-016, ADR-022 Decision 2/8).
 *
 * One row per AcademicDocument. Persists only the SHA-256 digest of the opaque
 * verification token and the minimal tenant-routing metadata; no student data,
 * grades or document content. token_hash is the unique lookup key; the digest
 * column is independent of the small controlled status enum above it.
 */
return new class () extends Migration {
    public function up(): void
    {
        Schema::create('verification_identities', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique();
            $table->string('school_id', 36);
            $table->unsignedBigInteger('academic_document_id');
            $table->char('token_hash', 64);
            $table->string('status', 20)->default('active');
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('academic_document_id')->references('id')->on('academic_documents')->cascadeOnDelete();
            $table->unique('academic_document_id', 'verification_identities_document_unique');
            $table->unique('token_hash', 'verification_identities_token_hash_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_identities');
    }
};
