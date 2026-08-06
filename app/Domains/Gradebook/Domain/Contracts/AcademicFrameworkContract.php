<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\Contracts;

use App\Domains\Gradebook\Domain\References\ClassReference;
use App\Domains\Gradebook\Domain\References\SectionReference;
use App\Domains\Gradebook\Domain\References\SubjectReference;
use App\Domains\Gradebook\Domain\References\TermReference;
use App\Domains\Gradebook\Domain\References\YearReference;

/**
 * Read-only gateway to the Academic Framework (SPR-010). The Gradebook engine
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

    public function schoolClass(string $schoolId, int|string $classId): ?ClassReference;

    public function section(string $schoolId, int|string $sectionId): ?SectionReference;
}
