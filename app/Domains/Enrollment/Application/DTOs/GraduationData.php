<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Application\DTOs;

use App\Domains\Enrollment\Domain\Enums\GraduationDecision;

final readonly class GraduationData
{
    public function __construct(
        public GraduationDecision $decision,
        public int $educationLevelId,
        public string $graduatedOn,
        public ?string $certificateReference = null,
        public ?string $notes = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            decision: GraduationDecision::from((string) $data['decision']),
            educationLevelId: (int) $data['education_level_id'],
            graduatedOn: (string) $data['graduated_on'],
            certificateReference: $data['certificate_reference'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }
}
