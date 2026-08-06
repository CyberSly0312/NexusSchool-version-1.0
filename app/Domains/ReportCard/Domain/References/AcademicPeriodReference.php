<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Domain\References;

/**
 * Academic frame reference of a report card run (SPR-011).
 */
final readonly class AcademicPeriodReference
{
    public function __construct(
        public int|string|null $classId = null,
        public ?string $className = null,
        public int|string|null $sectionId = null,
        public ?string $sectionName = null,
        public int|string|null $termId = null,
        public ?string $termName = null,
        public int|string|null $yearId = null,
        public ?string $yearName = null,
    ) {
    }
}
