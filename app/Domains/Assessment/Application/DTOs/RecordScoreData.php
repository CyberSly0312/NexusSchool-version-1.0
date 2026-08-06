<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Application\DTOs;

/**
 * Data transfer object for recording or updating a single score (SPR-009).
 */
final readonly class RecordScoreData
{
    public function __construct(
        public int|string $assessmentId,
        public int|string $enrollmentId,
        public float $score,
        public int|string|null $componentId = null,
        public ?string $feedback = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            assessmentId: (int) $data['assessment_id'],
            enrollmentId: (int) $data['enrollment_id'],
            score: (float) $data['score'],
            componentId: isset($data['assessment_component_id']) ? (int) $data['assessment_component_id'] : null,
            feedback: self::nullableString($data['feedback'] ?? null),
        );
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
