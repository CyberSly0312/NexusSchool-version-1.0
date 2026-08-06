<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\Contracts;

use App\Domains\Assessment\Domain\References\SubjectReference;
use App\Domains\Assessment\Domain\References\TermReference;
use App\Domains\Assessment\Domain\References\YearReference;

/**
 * Read-only gateway to the Academic Framework (SPR-009). The Assessment engine
 * never talks to Academic models directly; implementations live in the
 * Infrastructure layer of this domain.
 */
interface AcademicFrameworkContract
{
    public function year(string $schoolId, int|string $yearId): ?YearReference;

    public function activeYear(string $schoolId): ?YearReference;

    public function term(string $schoolId, int|string $termId): ?TermReference;

    /**
     * @return list<TermReference>
     */
    public function terms(string $schoolId, int|string $yearId): array;

    public function subject(string $schoolId, int|string $subjectId): ?SubjectReference;
}
