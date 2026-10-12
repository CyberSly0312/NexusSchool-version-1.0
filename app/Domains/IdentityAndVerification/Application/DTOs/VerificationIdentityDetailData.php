<?php

declare(strict_types=1);

namespace App\Domains\IdentityAndVerification\Application\DTOs;

use App\Domains\IdentityAndVerification\Domain\Enums\VerificationEligibilityVerdict;

/**
 * Result of resolving a verification token (SPR-016, ADR-022 Decision 2/3).
 *
 * Composes the verification identity with the document lifecycle state so the
 * verification surface can decide the verdict. Never contains grades, content,
 * personal data or the token itself.
 */
final class VerificationIdentityDetailData
{
    public function __construct(
        public readonly string $identityUuid,
        public readonly string $schoolId,
        public readonly int|string $documentId,
        public readonly string $status,
        public readonly string $documentStatus,
        public readonly VerificationEligibilityVerdict $verdict,
        public readonly bool $publiclyVerifiable,
        public readonly ?\Illuminate\Support\Carbon $issuedAt = null,
    ) {
    }
}
