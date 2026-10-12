<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lifecycle columns for verification identities (SPR-016 Phase 4,
 * ADR-022 Decision 4).
 *
 * Purely additive: revocation / replacement metadata for the forward-only
 * Active → Revoked / Active → Replaced transitions. The recorded reason is
 * internal (identity + audit) and never exported by the public surface. No
 * data is destroyed on down; the columns are simply dropped.
 */
return new class () extends Migration {
    public function up(): void
    {
        Schema::table('verification_identities', function (Blueprint $table) {
            $table->string('revocation_reason', 1000)->nullable()->after('status');
            $table->timestamp('revoked_at')->nullable()->after('revocation_reason');
            $table->timestamp('replaced_at')->nullable()->after('revoked_at');
            $table->string('replaced_by_document_number', 255)->nullable()->after('replaced_at');
        });
    }

    public function down(): void
    {
        Schema::table('verification_identities', function (Blueprint $table) {
            $table->dropColumn(['revocation_reason', 'revoked_at', 'replaced_at', 'replaced_by_document_number']);
        });
    }
};
