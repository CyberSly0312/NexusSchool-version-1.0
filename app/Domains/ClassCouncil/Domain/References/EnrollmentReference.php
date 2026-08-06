<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Domain\References;

/**
 * An active enrollment of a council's class, bound to its student (SPR-013).
 */
final readonly class EnrollmentReference
{
    public function __construct(
        public int|string $id,
        public StudentReference $student,
    ) {
    }
}
