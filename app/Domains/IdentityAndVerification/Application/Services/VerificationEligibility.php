<?php

declare(strict_types=1);

namespace App\Domains\IdentityAndVerification\Application\Services;

use App\Domains\IdentityAndVerification\Domain\Enums\VerificationEligibilityVerdict;

/**
 * Maps the authoritative Academic Communication document lifecycle (SPR-012
 * DocumentStatus) to the public verification verdict (ADR-022 Decision 7).
 *
 * A pure projection of the existing lifecycle: unpublished documents are never
 * publicly verifiable, Published is the only "currently valid" answer, and
 * Archived remains historically verifiable. No second lifecycle engine exists
 * in SPR-016.
 */
final class VerificationEligibility
{
    public function verdictForDocumentStatus(string $status): VerificationEligibilityVerdict
    {
        return match ($status) {
            'published' => VerificationEligibilityVerdict::AuthenticPublished,
            'archived' => VerificationEligibilityVerdict::AuthenticArchived,
            default => VerificationEligibilityVerdict::NotPubliclyVerifiable,
        };
    }

    public function isPubliclyVerifiable(string $status): bool
    {
        return $this->verdictForDocumentStatus($status) !== VerificationEligibilityVerdict::NotPubliclyVerifiable;
    }
}
