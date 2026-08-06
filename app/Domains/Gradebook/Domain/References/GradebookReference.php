<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\References;

/**
 * Read-only projection of a gradebook aggregate (SPR-010): one subject, one
 * class, one term. Free of any concrete model.
 */
final readonly class GradebookReference
{
    public function __construct(
        public int|string $id,
        public YearReference $year,
        public TermReference $term,
        public SubjectReference $subject,
        public ClassReference $class,
        public ?SectionReference $section = null,
        public ?TeacherReference $teacher = null,
        public float $coefficient = 1.0,
        public string $status = 'draft',
    ) {
    }
}
