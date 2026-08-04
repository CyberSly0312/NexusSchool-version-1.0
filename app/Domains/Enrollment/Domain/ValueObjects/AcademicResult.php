<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * Immutable snapshot of the academic result that drove a promotion decision.
 * Stored as JSON on the promotion record so the decision is auditable even if
 * the grading system evolves later.
 */
final readonly class AcademicResult
{
    public function __construct(
        public float $average,
        public ?int $rank = null,
        public ?float $highest = null,
        public ?float $lowest = null,
    ) {
        if ($this->average < 0 || $this->average > 100) {
            throw new InvalidArgumentException('The academic average must be between 0 and 100.');
        }

        if ($this->rank !== null && $this->rank < 1) {
            throw new InvalidArgumentException('The academic rank must be a positive integer.');
        }

        if ($this->highest !== null && ($this->highest < 0 || $this->highest > 100)) {
            throw new InvalidArgumentException('The highest mark must be between 0 and 100.');
        }

        if ($this->lowest !== null && ($this->lowest < 0 || $this->lowest > 100)) {
            throw new InvalidArgumentException('The lowest mark must be between 0 and 100.');
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            average: (float) ($data['average'] ?? 0),
            rank: isset($data['rank']) ? (int) $data['rank'] : null,
            highest: isset($data['highest']) ? (float) $data['highest'] : null,
            lowest: isset($data['lowest']) ? (float) $data['lowest'] : null,
        );
    }

    /**
     * @return array{average: float, rank: int|null, highest: float|null, lowest: float|null}
     */
    public function toArray(): array
    {
        return [
            'average' => $this->average,
            'rank' => $this->rank,
            'highest' => $this->highest,
            'lowest' => $this->lowest,
        ];
    }
}
