<?php

declare(strict_types=1);

namespace App\Domains\IdentityAndVerification\Domain\References;

/**
 * Verification-relevant projection of an AcademicDocument (SPR-016,
 * ADR-022 I10 / C1).
 *
 * Exposes only the metadata the public verification surface is allowed to
 * consume — document identity, owning school, lifecycle status and the public
 * LEVEL-2 fields (document number, document type, issue date). It never
 * carries grades, transcript content, personal data or internal identifiers
 * beyond the document key.
 */
final class DocumentVerificationState
{
    public function __construct(
        public readonly string $schoolId,
        public readonly int|string $documentId,
        public readonly string $documentStatus,
        public readonly string $documentNumber,
        public readonly string $documentType,
        public readonly ?string $generatedAt = null,
    ) {
    }
}
