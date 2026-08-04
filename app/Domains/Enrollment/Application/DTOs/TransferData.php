<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Application\DTOs;

use App\Domains\Enrollment\Domain\Enums\TransferType;

final readonly class TransferData
{
    public function __construct(
        public TransferType $transferType,
        public string $transferDate,
        public ?int $toClassId = null,
        public ?int $toSectionId = null,
        public ?string $toSchoolId = null,
        public ?string $fromSchoolId = null,
        public ?string $reason = null,
        public ?string $notes = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            transferType: TransferType::from((string) $data['transfer_type']),
            transferDate: (string) $data['transfer_date'],
            toClassId: isset($data['to_class_id']) ? (int) $data['to_class_id'] : null,
            toSectionId: isset($data['to_section_id']) ? (int) $data['to_section_id'] : null,
            toSchoolId: $data['to_school_id'] ?? null,
            fromSchoolId: $data['from_school_id'] ?? null,
            reason: $data['reason'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }
}
