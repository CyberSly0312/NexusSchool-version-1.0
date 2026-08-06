<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Domain\Contracts;

use App\Domains\ClassCouncil\Domain\References\GradebookStandingReference;

/**
 * Read-only gateway to the Gradebook engine (SPR-013). The Class Council
 * engine reads the published student aggregates verbatim to derive the
 * automatic decision it may override; it never recomputes a figure.
 */
interface GradebookContract
{
    /**
     * Student-level aggregates of one student over one period.
     */
    public function studentStanding(
        string $schoolId,
        int|string $studentId,
        int|string $yearId,
        int|string $termId,
    ): ?GradebookStandingReference;
}
