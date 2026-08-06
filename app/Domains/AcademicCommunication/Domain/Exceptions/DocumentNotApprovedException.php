<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Exceptions;

/**
 * Thrown when publication is attempted on a document whose policy requires
 * approval but the approval has not been granted yet.
 */
final class DocumentNotApprovedException extends AcademicCommunicationException
{
    public static function forDocument(int|string $documentId): self
    {
        return new self(sprintf('Document [#%s] requires approval before publication.', $documentId));
    }
}
