<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Exceptions;

/**
 * Thrown when a mutation targets a document (or version) that has already been
 * published. Published versions are immutable and are never overwritten; any
 * change must produce a new version.
 */
final class DocumentImmutableException extends AcademicCommunicationException
{
    public static function forDocument(int|string $documentId, string $status): self
    {
        return new self(
            sprintf('Document [#%s] is [%s] and cannot be modified.', $documentId, $status),
        );
    }

    public static function forVersion(int|string $documentId, int $version): self
    {
        return new self(
            sprintf('Document [#%s] version [%d] is immutable once published.', $documentId, $version),
        );
    }
}
