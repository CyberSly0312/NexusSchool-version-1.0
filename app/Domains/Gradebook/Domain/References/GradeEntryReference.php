<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\References;

use App\Domains\Gradebook\Domain\Enums\Appreciation;
use App\Domains\Gradebook\Domain\Enums\LetterGrade;

/**
 * The computed term grade of one student in one subject (SPR-010).
 *
 * Carries no model: percentages are canonical and the average is the same
 * value projected onto the configured grading scale (20 or 100).
 */
final readonly class GradeEntryReference
{
    public function __construct(
        public StudentReference $student,
        public float $percentage,
        public float $average,
        public LetterGrade $letterGrade,
        public ?int $rank = null,
        public ?int $rankDenominator = null,
    ) {
    }

    public function gradePoint(): float
    {
        return $this->letterGrade->gradePoint();
    }

    public function appreciation(): string
    {
        return $this->letterGrade->appreciation();
    }

    public function appreciationEnum(int $scale = 20): Appreciation
    {
        return Appreciation::fromAverage($this->average, $scale);
    }

    public function passes(): bool
    {
        return $this->letterGrade->passes();
    }
}
