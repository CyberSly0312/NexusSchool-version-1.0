<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Contracts;

use App\Domains\AcademicCommunication\Domain\References\GuardianReference;

/**
 * Read-only gateway to the Guardian domain (SPR-012). Guardians are the
 * recipients of parent summaries and report cards — they only ever receive the
 * data of the children they are linked to.
 */
interface GuardianContract
{
    /**
     * Guardians linked to one student.
     *
     * @return list<GuardianReference>
     */
    public function guardiansForStudent(string $schoolId, int|string $studentId): array;
}
