<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Domain\Contracts;

use App\Domains\ReportCard\Domain\References\CouncilDecisionReference;

/**
 * Read-only gateway to the Class Council engine (SPR-011).
 *
 * The council adapter is a stub returning an empty list — the Class Council
 * engine ships in a later sprint. The Report Card engine integrates through
 * this contract only, so the stub can be replaced without touching the domain.
 */
interface ClassCouncilContract
{
    /**
     * Council decisions affecting one student over one period.
     *
     * @return list<CouncilDecisionReference>
     */
    public function decisionsForStudent(
        string $schoolId,
        int|string $studentId,
        int|string $yearId,
        int|string $termId,
    ): array;
}
