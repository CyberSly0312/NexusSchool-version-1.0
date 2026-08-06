<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Contracts;

use App\Domains\AcademicCommunication\Domain\References\AcademicYearReference;
use App\Domains\AcademicCommunication\Domain\References\ClassReference;
use App\Domains\AcademicCommunication\Domain\References\SectionReference;
use App\Domains\AcademicCommunication\Domain\References\TermReference;

/**
 * Read-only gateway to the Academic Framework domain (SPR-012).
 *
 * Provides the academic frame of a document — the school year, term, class and
 * section — whose labels appear in the document and in its permanent identity.
 * Implementations live in the Infrastructure layer of this engine and are the
 * only place allowed to read Academic-domain models.
 */
interface AcademicFrameworkContract
{
    public function year(string $schoolId, int|string $yearId): ?AcademicYearReference;

    public function term(string $schoolId, int|string $termId): ?TermReference;

    public function class(string $schoolId, int|string $classId): ?ClassReference;

    public function section(string $schoolId, int|string $sectionId): ?SectionReference;
}
