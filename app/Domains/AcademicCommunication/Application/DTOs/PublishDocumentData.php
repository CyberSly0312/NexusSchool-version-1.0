<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Application\DTOs;

/**
 * A publication request (SPR-012): which document to publish, through which
 * channels, for which recipients.
 */
final readonly class PublishDocumentData
{
    /**
     * @param  list<string>  $channels
     */
    public function __construct(
        public string $schoolId,
        public int|string $documentId,
        public array $channels = [],
        public int|string|null $actorId = null,
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
            channels: $data['channels'] ?? [],
            actorId: $data['actor_id'] ?? null,
        );
    }
}
