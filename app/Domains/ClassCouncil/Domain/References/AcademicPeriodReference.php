<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Domain\References;

/**
 * The validated academic frame of a council session (SPR-013). Supplied by the
 * Academic framework through the contract; the Class Council engine never
 * touches the Academic models.
 */
final readonly class AcademicPeriodReference
{
    public function __construct(
        public int|string $yearId,
        public ?string $yearName = null,
        public int|string|null $termId = null,
        public ?string $termName = null,
        public int|string|null $classId = null,
        public ?string $className = null,
        public int|string|null $sectionId = null,
        public ?string $sectionName = null,
    ) {
    }
}
