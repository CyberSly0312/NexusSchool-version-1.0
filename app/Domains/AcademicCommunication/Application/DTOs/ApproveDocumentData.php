<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Application\DTOs;

/**
 * An approval decision on a document (SPR-012).
 */
final readonly class ApproveDocumentData
{
    public function __construct(
        public string $schoolId,
        public int|string $documentId,
        public string $approverRole,
        public bool $approved,
        public ?string $comment = null,
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
            approverRole: (string) $data['approver_role'],
            approved: (bool) ($data['approved'] ?? true),
            comment: $data['comment'] ?? null,
            actorId: $data['actor_id'] ?? null,
        );
    }
}
