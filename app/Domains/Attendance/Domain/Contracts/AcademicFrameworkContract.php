<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\Contracts;

use App\Domains\Attendance\Domain\References\ClassReference;
use App\Domains\Attendance\Domain\References\SectionReference;
use App\Domains\Attendance\Domain\References\SubjectReference;
use App\Domains\Attendance\Domain\References\TermReference;
use App\Domains\Attendance\Domain\References\YearReference;

/**
 * Read-only gateway to the Academic Framework (SPR-008). The Attendance engine
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

    public function class(string $schoolId, int|string $classId): ?ClassReference;

    /**
     * @return list<ClassReference>
     */
    public function classes(string $schoolId): array;

    public function section(string $schoolId, int|string $sectionId): ?SectionReference;

    public function subject(string $schoolId, int|string $subjectId): ?SubjectReference;
}
