<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Application\DTOs;

/**
 * A review request (SPR-012): which document, in which school, must be marked
 * as reviewed. The school is the authoritative tenant context and the document
 * is resolved inside it.
 */
final readonly class ReviewDocumentData
{
    public function __construct(
        public string $schoolId,
        public int|string $documentId,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: (string) $data['school_id'],
            documentId: $data['document_id'],
        );
    }
}
