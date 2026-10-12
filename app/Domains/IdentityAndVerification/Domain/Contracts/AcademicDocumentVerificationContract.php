<?php

declare(strict_types=1);

namespace App\Domains\IdentityAndVerification\Domain\Contracts;

use App\Domains\IdentityAndVerification\Domain\References\DocumentVerificationState;

/**
 * Read-only gateway to the Academic Communication substrate for verification
 * (SPR-016, ADR-022 C1 / I10).
 *
 * SPR-016 Domain and Application layers never import an AcademicDocument (or
 * any foreign entity): document reads go through this contract, implemented by
 * the Infrastructure adapter. The projection is strictly verification-relevant
 * and never exposes grades, content or personal data.
 */
interface AcademicDocumentVerificationContract
{
    /**
     * Return the verification-relevant state of a document owned by the given
     * school, or null when no such document exists for that school.
     */
    public function stateForDocument(string $schoolId, int|string $documentId): ?DocumentVerificationState;
}
