<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\Contracts;

use App\Domains\Assessment\Domain\References\StudentReference;

/**
 * Read-only gateway to the Student identity domain (SPR-009). Implementations
 * live in the Infrastructure layer of this domain.
 */
interface StudentIdentityContract
{
    public function student(string $schoolId, int|string $studentId): ?StudentReference;

    /**
     * @param  list<int|string>  $studentIds
     *
     * @return list<StudentReference>
     */
    public function students(string $schoolId, array $studentIds): array;
}
