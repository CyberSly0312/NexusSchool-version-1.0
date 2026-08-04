<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Application\DTOs;

final readonly class UpdateEnrollmentData
{
    public function __construct(
        public ?int $classSectionId = null,
        public ?string $notes = null,
        public ?string $enrolledAt = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            classSectionId: isset($data['class_section_id']) ? (int) $data['class_section_id'] : null,
            notes: $data['notes'] ?? null,
            enrolledAt: $data['enrolled_at'] ?? null,
        );
    }
}
