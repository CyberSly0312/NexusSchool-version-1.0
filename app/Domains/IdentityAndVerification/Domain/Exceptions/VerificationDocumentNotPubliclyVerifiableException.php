<?php

declare(strict_types=1);

namespace App\Domains\IdentityAndVerification\Domain\Exceptions;

final class VerificationDocumentNotPubliclyVerifiableException extends VerificationIdentityException
{
    public static function forDocument(int|string $documentId, string $status): self
    {
        return new self(sprintf(
            'Document [%s] is [%s] and cannot be revoked or replaced: only published or archived documents are publicly revocable.',
            (string) $documentId,
            $status,
        ));
    }
}
