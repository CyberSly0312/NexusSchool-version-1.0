<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Domain\Contracts;

use App\Domains\ClassCouncil\Domain\References\AcademicPeriodReference;

/**
 * Read-only gateway to the Academic framework (SPR-013). The Class Council
 * engine validates its session's frame through this contract; it never touches
 * the Academic models directly.
 */
interface AcademicFrameworkContract
{
    public function period(
        string $schoolId,
        int|string $yearId,
        int|string $termId,
        int|string|null $classId = null,
        int|string|null $sectionId = null,
    ): ?AcademicPeriodReference;
}
