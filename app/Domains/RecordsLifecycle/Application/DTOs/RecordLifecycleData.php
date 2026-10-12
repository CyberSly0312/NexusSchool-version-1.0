<?php

declare(strict_types=1);

namespace App\Domains\RecordsLifecycle\Application\DTOs;

final class RecordLifecycleData
{
    public function __construct(
        public readonly string $schoolId,
        public readonly string $sourceDomain,
        public readonly string $sourceType,
        public readonly int|string $sourceId,
        public readonly ?string $sourceVersion = null,
        public readonly ?string $lifecycleState = null,
        public readonly ?array $academicContext = null,
        public readonly ?int $createdBy = null,
        public readonly ?string $originEvent = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: (string) $data['school_id'],
            sourceDomain: (string) $data['source_domain'],
            sourceType: (string) $data['source_type'],
            sourceId: $data['source_id'],
            sourceVersion: $data['source_version'] ?? null,
            lifecycleState: $data['lifecycle_state'] ?? null,
            academicContext: $data['academic_context'] ?? null,
            createdBy: $data['created_by'] ?? null,
            originEvent: $data['origin_event'] ?? null,
        );
    }
}
