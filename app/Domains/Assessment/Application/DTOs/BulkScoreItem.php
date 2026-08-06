<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Application\DTOs;

/**
 * One row of a bulk score write (SPR-009). CSV-ready: keys mirror the score
 * table columns so exports can be fed straight back in.
 */
final readonly class BulkScoreItem
{
    public function __construct(
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
