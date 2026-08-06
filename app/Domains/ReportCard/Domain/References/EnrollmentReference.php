<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Domain\References;

/**
 * An active enrollment of a run's class, bound to its student (SPR-011).
 */
final readonly class EnrollmentReference
{
    public function __construct(
        public int|string $id,
        public StudentReference $student,
    ) {
    }
}
