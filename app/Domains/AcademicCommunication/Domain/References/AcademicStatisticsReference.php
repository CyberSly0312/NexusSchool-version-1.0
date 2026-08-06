<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\References;

/**
 * School-level academic statistics consumed for the academic statistics
 * document and the school chronicle (SPR-012).
 */
final readonly class AcademicStatisticsReference
{
    /**
     * @param  array<string, mixed>  $metrics
     */
    public function __construct(
        public string $periodLabel,
        public int $studentCount,
        public float $globalAverage,
        public float $passRate,
        public int $honorsCount,
        public array $metrics = [],
    ) {
    }
}
