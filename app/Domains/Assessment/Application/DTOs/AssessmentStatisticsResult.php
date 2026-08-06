<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Application\DTOs;

/**
 * Result of GenerateAssessmentStatisticsAction (SPR-009).
 */
final readonly class AssessmentStatisticsResult
{
    public function __construct(
        public int $totalScores,
        public float $average,
        public float $minimum,
        public float $maximum,
        public float $median,
        public float $passRate,
        public float $completionRate,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'total_scores' => $this->totalScores,
            'average' => $this->average,
            'minimum' => $this->minimum,
            'maximum' => $this->maximum,
            'median' => $this->median,
            'pass_rate' => $this->passRate,
            'completion_rate' => $this->completionRate,
        ];
    }
}
