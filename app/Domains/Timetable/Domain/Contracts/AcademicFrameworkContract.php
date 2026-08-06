<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\Contracts;

use App\Domains\Timetable\Domain\References\ClassReference;
use App\Domains\Timetable\Domain\References\SectionReference;
use App\Domains\Timetable\Domain\References\SubjectReference;
use App\Domains\Timetable\Domain\References\TermReference;
use App\Domains\Timetable\Domain\References\YearReference;

/**
 * Read-only gateway to the Academic Framework. The Timetable engine never talks
 * to Academic models directly: it only ever sees the value-object projections
 * below. Implementations live in the Infrastructure layer of this domain.
 */
interface AcademicFrameworkContract
{
    /**
     * @return list<YearReference>
     */
    public function years(string $schoolId): array;

    public function year(string $schoolId, int|string $yearId): ?YearReference;

    /**
     * @return list<TermReference>
     */
    public function terms(string $schoolId, int|string $yearId): array;

    public function term(string $schoolId, int|string $termId): ?TermReference;

    /**
     * @return list<ClassReference>
     */
    public function classes(string $schoolId, int|string|null $yearId = null): array;

    public function class(string $schoolId, int|string $classId): ?ClassReference;

    /**
     * @return list<SectionReference>
     */
    public function sections(string $schoolId, int|string|null $classId = null): array;

    public function section(string $schoolId, int|string $sectionId): ?SectionReference;

    /**
     * @return list<SubjectReference>
     */
    public function subjects(string $schoolId): array;

    public function subject(string $schoolId, int|string $subjectId): ?SubjectReference;

    /**
     * Subjects a class follows, in teaching order.
     *
     * @return list<SubjectReference>
     */
    public function classSubjects(string $schoolId, int|string $classId): array;
}
