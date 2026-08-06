<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Infrastructure\Contracts;

use App\Domains\Gradebook\Domain\Contracts\AcademicFrameworkContract;
use App\Domains\Gradebook\Domain\Contracts\GradebookContract;
use App\Domains\Gradebook\Domain\References\GradebookReference;
use App\Domains\Gradebook\Infrastructure\Models\Gradebook;

/**
 * Maps the Gradebook model onto the domain GradebookContract (SPR-010).
 */
final class GradebookQueryAdapter implements GradebookContract
{
    public function __construct(
        private readonly AcademicFrameworkContract $academic,
    ) {
    }

    public function gradebook(string $schoolId, int|string $gradebookId): ?GradebookReference
    {
        $gradebook = Gradebook::query()->forSchool($schoolId)->find($gradebookId);

        if ($gradebook === null) {
            return null;
        }

        $year = $this->academic->year($schoolId, $gradebook->academic_calendar_year_id);
        $term = $this->academic->term($schoolId, $gradebook->academic_calendar_term_id);
        $subject = $this->academic->subject($schoolId, $gradebook->academic_subject_id);
        $class = $this->academic->schoolClass($schoolId, $gradebook->school_class_id);
        $section = $gradebook->class_section_id !== null
            ? $this->academic->section($schoolId, $gradebook->class_section_id)
            : null;

        if ($year === null || $term === null || $subject === null || $class === null) {
            return null;
        }

        return new GradebookReference(
            id: $gradebook->getKey(),
            year: $year,
            term: $term,
            subject: $subject,
            class: $class,
            section: $section,
            teacher: null,
            coefficient: (float) $gradebook->coefficient,
            status: $gradebook->status->value,
        );
    }
}
