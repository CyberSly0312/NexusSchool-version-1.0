<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Exceptions;

/**
 * Thrown when a document generation request is missing the data required to
 * resolve a contract (e.g. no student for a student-scoped document).
 */
final class MissingDocumentContextException extends AcademicCommunicationException
{
    public static function missing(string $contextKey): self
    {
        return new self(sprintf('Missing document generation context: [%s].', $contextKey));
    }
}
