<?php

declare(strict_types=1);

namespace App\Domains\User\DTOs;

use App\Domains\User\Enums\EnrollmentProvisioningKind;
use App\Models\User;

/**
 * Trusted application input for Task 11.2 enrollment-linked provisioning.
 *
 * `schoolId` is a consistency assertion only. The service derives the school
 * from tenancy context and rejects any mismatch. Role, purpose and ownership
 * are never accepted from a client-supplied value.
 */
final readonly class EnrollmentProvisioningRequest
{
    private function __construct(
        public EnrollmentProvisioningKind $kind,
        public string $schoolId,
        public string $enrollmentId,
        public User $actor,
        public bool $explicitSchoolRequest,
        public ?StudentEligibilityEvidence $studentEvidence,
        public ?ParentPortalAccessRequest $parentRequest,
        public string $email,
        public string $firstName,
        public string $lastName,
        public ?string $phone,
    ) {
    }

    public static function forStudent(
        string $schoolId,
        string $enrollmentId,
        User $actor,
        bool $explicitSchoolRequest,
        ?StudentEligibilityEvidence $studentEvidence,
        string $email,
        string $firstName,
        string $lastName,
        ?string $phone = null,
    ): self {
        return new self(
            EnrollmentProvisioningKind::Student,
            $schoolId,
            $enrollmentId,
            $actor,
            $explicitSchoolRequest,
            $studentEvidence,
            null,
            $email,
            $firstName,
            $lastName,
            $phone,
        );
    }

    public static function forParent(
        string $schoolId,
        string $enrollmentId,
        User $actor,
        bool $explicitSchoolRequest,
        ?ParentPortalAccessRequest $parentRequest,
        string $email,
        string $firstName,
        string $lastName,
        ?string $phone = null,
    ): self {
        return new self(
            EnrollmentProvisioningKind::Parent,
            $schoolId,
            $enrollmentId,
            $actor,
            $explicitSchoolRequest,
            null,
            $parentRequest,
            $email,
            $firstName,
            $lastName,
            $phone,
        );
    }
}
