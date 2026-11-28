<?php

declare(strict_types=1);

use App\Domains\Guardian\Domain\Enums\RelationshipPortalAccessStatus;
use App\Domains\Guardian\Domain\Enums\RelationshipVerificationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Adds the minimal explicit state required for Task 11.3 child-level
     * authorization. Existing durable rows remain valid business relationships
     * but default to unverified/unauthorized, so no legacy row silently grants
     * Parent portal access. No responsibility flags or relationship types are
     * changed.
     */
    public function up(): void
    {
        Schema::table('student_guardians', function (Blueprint $table): void {
            $table->string('verification_status', 32)
                ->default(RelationshipVerificationStatus::Unverified->value)
                ->after('notes');
            $table->string('portal_access_status', 32)
                ->default(RelationshipPortalAccessStatus::NotAuthorized->value)
                ->after('verification_status');
            $table->timestamp('verified_at')->nullable()->after('portal_access_status');
            $table->timestamp('portal_authorized_at')->nullable()->after('verified_at');
            $table->timestamp('portal_revoked_at')->nullable()->after('portal_authorized_at');
            $table->string('verification_evidence_code', 64)->nullable()->after('portal_revoked_at');
            $table->string('authorization_reason_code', 64)->nullable()->after('verification_evidence_code');

            $table->index(
                ['school_id', 'guardian_id', 'portal_access_status'],
                'sg_school_guardian_portal_status_index',
            );
            $table->index(
                ['school_id', 'student_id', 'portal_access_status'],
                'sg_school_student_portal_status_index',
            );
        });
    }

    public function down(): void
    {
        Schema::table('student_guardians', function (Blueprint $table): void {
            $table->dropIndex('sg_school_guardian_portal_status_index');
            $table->dropIndex('sg_school_student_portal_status_index');
            $table->dropColumn([
                'verification_status',
                'portal_access_status',
                'verified_at',
                'portal_authorized_at',
                'portal_revoked_at',
                'verification_evidence_code',
                'authorization_reason_code',
            ]);
        });
    }
};
