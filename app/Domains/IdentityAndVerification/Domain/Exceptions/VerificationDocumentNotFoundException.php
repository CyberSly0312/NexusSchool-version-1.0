<?php

declare(strict_types=1);

namespace App\Domains\IdentityAndVerification\Domain\Exceptions;

final class VerificationDocumentNotFoundException extends VerificationIdentityException
{
    public static function forSchoolAndDocument(string $schoolId, int|string $documentId): self
    {
        return new self(sprintf(
            'No verifiable document [%s] exists for school [%s].',
            (string) $documentId,
            $schoolId,
        ));
    }
}
