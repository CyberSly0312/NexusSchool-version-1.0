<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\References;

/**
 * Class-level gradebook aggregate consumed for teacher summaries, academic
 * statistics and the school chronicle (SPR-012).
 */
final readonly class GradebookSummaryReference
{
    /**
     * @param  list<GradebookEntryReference>  $entries
     */
    public function __construct(
        public ClassReference $class,
        public TermReference $term,
        public float $classAverage,
        public float $passRate,
        public array $entries = [],
    ) {
    }
}
