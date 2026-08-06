<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Domain\References;

/**
 * One review item of a convened council session (SPR-013): the active student,
 * their published academic standing and the automatic decision the council may
 * confirm or override.
 */
final readonly class CouncilReviewReference
{
    public function __construct(
        public int|string $enrollmentId,
        public StudentReference $student,
        public ?GradebookStandingReference $standing,
        public ?AutomaticDecisionReference $automatic = null,
    ) {
    }
}
