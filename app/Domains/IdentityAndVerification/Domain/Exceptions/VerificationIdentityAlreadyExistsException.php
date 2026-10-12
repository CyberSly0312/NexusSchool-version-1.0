<?php

declare(strict_types=1);

namespace App\Domains\IdentityAndVerification\Domain\Exceptions;

final class VerificationIdentityAlreadyExistsException extends VerificationIdentityException
{
    public static function forDocument(int|string $documentId): self
    {
        return new self(sprintf(
            'A verification identity already exists for document [%s].',
            (string) $documentId,
        ));
    }
}
