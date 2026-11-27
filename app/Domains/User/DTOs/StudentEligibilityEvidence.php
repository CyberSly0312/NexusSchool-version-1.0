<?php

declare(strict_types=1);

namespace App\Domains\User\DTOs;

/**
 * Typed trusted assertions from an approved school-side student request.
 *
 * The owner approved the need for eligibility/contact evidence but did not
 * define an accepted document list. This DTO therefore carries an opaque,
 * internal reference and never interprets, exposes or audits its content.
 */
final readonly class StudentEligibilityEvidence
{
    public string $evidenceReference;

    public function __construct(
        public bool $eligibilityConfirmed,
        public bool $contactEvidenceConfirmed,
        string $evidenceReference,
    ) {
        $this->evidenceReference = trim($evidenceReference);
    }

    public function isComplete(): bool
    {
        return $this->eligibilityConfirmed
            && $this->contactEvidenceConfirmed
            && $this->evidenceReference !== '';
    }
}
