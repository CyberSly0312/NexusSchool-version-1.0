<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Contracts;

use App\Domains\AcademicCommunication\Domain\References\StaffReference;

/**
 * Read-only gateway to the Staff domain (SPR-012). Staff are the signatories
 * and the recipients of teaching summaries.
 */
interface StaffContract
{
    public function staff(string $schoolId, int|string $staffId): ?StaffReference;

    /**
     * @param  list<int|string>  $staffIds
     *
     * @return list<StaffReference>
     */
    public function staffMembers(string $schoolId, array $staffIds): array;
}
