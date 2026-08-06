<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Domain\Contracts;

use App\Domains\ReportCard\Domain\References\GradebookEntryReference;
use App\Domains\ReportCard\Domain\References\GradebookStandingReference;

/**
 * Read-only gateway to the Gradebook engine (SPR-011). The Gradebook engine
 * supplies validated term grades and the student-level aggregates (GPA, CGPA,
 * overall percentage, academic standing); the Report Card engine renders them
 * verbatim and never recomputes a figure.
 */
interface GradebookContract
{
    /**
     * Term gradebook lines of one student over one period.
     *
     * @return list<GradebookEntryReference>
     */
    public function studentGrades(
        string $schoolId,
        int|string $studentId,
        int|string $yearId,
        int|string $termId,
    ): array;

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
