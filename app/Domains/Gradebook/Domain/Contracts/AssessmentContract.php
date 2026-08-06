<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\Contracts;

use App\Domains\Gradebook\Domain\References\AssessmentReference;

/**
 * Read-only gateway to the Assessment engine (SPR-010).
 *
 * A term average is the weighted mean of the percentages the student earned on
 * the published assessments of the period. This contract exposes only the
 * published contributions, never drafts.
 */
interface AssessmentContract
{
    /**
     * Published assessment contributions of one student over one period.
     *
     * @return list<AssessmentReference>
     */
    public function studentAssessmentContributions(
        string $schoolId,
        int|string $studentId,
        int|string $yearId,
        int|string $termId,
        int|string $subjectId,
        int|string $classId,
        int|string|null $sectionId = null,
    ): array;

    /**
     * True when the period carries at least one published assessment matching
     * the gradebook scope. Used to decide whether a calculation can run.
     */
    public function hasPublishedAssessments(
        string $schoolId,
        int|string $yearId,
        int|string $termId,
        int|string $subjectId,
        int|string $classId,
        int|string|null $sectionId = null,
    ): bool;
}
