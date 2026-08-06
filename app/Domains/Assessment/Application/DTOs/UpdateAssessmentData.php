<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Application\DTOs;

use Carbon\CarbonImmutable;

/**
 * Data transfer object for updating a draft or scheduled assessment (SPR-009).
 * Only the mutable definition fields are editable; the subject, class and
 * academic period are fixed at creation time.
 */
final readonly class UpdateAssessmentData
{
    public function __construct(
        public int|string $assessmentId,
        public string $title,
        public float $maximumScore,
        public ?float $passingScore = null,
        public ?float $weight = null,
        public ?CarbonImmutable $scheduledAt = null,
        public ?string $description = null,
        public ?string $notes = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            assessmentId: (int) $data['assessment_id'],
            title: trim((string) $data['title']),
            maximumScore: (float) $data['maximum_score'],
            passingScore: isset($data['passing_score']) ? (float) $data['passing_score'] : null,
            weight: isset($data['weight']) ? (float) $data['weight'] : null,
            scheduledAt: self::nullableDateTime($data['scheduled_at'] ?? null),
            description: self::nullableString($data['description'] ?? null),
            notes: self::nullableString($data['notes'] ?? null),
        );
    }

    private static function nullableDateTime(mixed $value): ?CarbonImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }

        return CarbonImmutable::parse($value);
    }

    private static function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }
}
