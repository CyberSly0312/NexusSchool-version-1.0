<?php

declare(strict_types=1);

namespace App\Domains\IdentityAndVerification\Domain\Exceptions;

final class VerificationIdentityNotFoundException extends VerificationIdentityException
{
    public static function forDocument(string $schoolId, int|string $documentId): self
    {
        return new self(sprintf(
            'No verification identity exists for document [%s] in school [%s].',
            (string) $documentId,
            $schoolId,
        ));
    }
}
