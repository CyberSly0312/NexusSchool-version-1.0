<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Contracts;

use App\Domains\AcademicCommunication\Domain\References\GradebookEntryReference;
use App\Domains\AcademicCommunication\Domain\References\GradebookSummaryReference;

/**
 * Read-only gateway to the Gradebook engine (SPR-012). The Gradebook engine
 * supplies validated academic content; it never generates documents itself.
 * This contract exposes only what the Communication Engine may render.
 */
interface GradebookContract
{
    /**
     * Gradebook lines for one student over one period.
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
     * Class-level aggregate for one period.
     */
    public function classSummary(
        string $schoolId,
        int|string $classId,
        int|string $yearId,
        int|string $termId,
    ): ?GradebookSummaryReference;
}
