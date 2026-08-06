<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Application\DTOs;

/**
 * Data transfer object for a bulk score write on one assessment (SPR-009).
 *
 * @param  list<BulkScoreItem>  $items
 */
final readonly class BulkScoresData
{
    public function __construct(
        public int|string $assessmentId,
        public array $items,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $items = array_map(
            fn (array $item): BulkScoreItem => BulkScoreItem::fromArray($item),
            (array) ($data['items'] ?? []),
        );

        return new self(
            assessmentId: (int) $data['assessment_id'],
            items: $items,
        );
    }
}
