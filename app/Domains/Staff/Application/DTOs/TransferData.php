<?php

declare(strict_types=1);

namespace App\Domains\Staff\Application\DTOs;

/**
 * Data transfer object for transferring an employment between campuses.
 * The employment keeps its staff and contract; only the owning school
 * (campus) changes.
 */
final readonly class TransferData
{
    public function __construct(
        public string $fromSchoolId,
        public string $toSchoolId,
        public int $staffId,
        public ?string $effectiveDate = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            fromSchoolId: (string) $data['from_school_id'],
            toSchoolId: (string) $data['to_school_id'],
            staffId: (int) $data['staff_id'],
            effectiveDate: isset($data['effective_date']) && trim((string) $data['effective_date']) !== ''
                ? (string) $data['effective_date']
                : null,
        );
    }
}
