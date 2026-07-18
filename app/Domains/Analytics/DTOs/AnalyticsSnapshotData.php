<?php

declare(strict_types=1);

namespace App\Domains\Analytics\DTOs;

readonly class AnalyticsSnapshotData
{
    public function __construct(
        public ?string $id,
        public string $schoolId,
        public string $snapshotType,
        public array $data = [],
        public ?string $periodStart = null,
        public ?string $periodEnd = null,
        public ?string $generatedAt = null,
        public bool $isImmutable = true,
    ) {
    }

    public static function create(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            schoolId: $data['school_id'],
            snapshotType: $data['snapshot_type'],
            data: $data['data'] ?? [],
            periodStart: $data['period_start'] ?? null,
            periodEnd: $data['period_end'] ?? null,
            generatedAt: $data['generated_at'] ?? now()->toDateTimeString(),
            isImmutable: $data['is_immutable'] ?? true,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->schoolId,
            'snapshot_type' => $this->snapshotType,
            'data' => $this->data,
            'period_start' => $this->periodStart,
            'period_end' => $this->periodEnd,
            'generated_at' => $this->generatedAt,
            'is_immutable' => $this->isImmutable,
        ];
    }
}
