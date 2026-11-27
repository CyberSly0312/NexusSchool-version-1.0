<?php

declare(strict_types=1);

namespace App\Domains\IdentityMatching\Application\DTOs;

use App\Domains\IdentityMatching\Domain\Enums\MatchableEntityType;

/**
 * Input for school-scoped identity resolution.
 *
 * The school ID must come from trusted server-side tenant context. It is used
 * only as a consistency assertion; every query is built from the school
 * resolved by the current tenancy context, never from this value.
 *
 * No value here is trusted as proof of identity by itself. Durable IDs must be
 * "durable same-school identifiers" (student number, guardian number, Guardian
 * ID, User ID), not browser-controlled labels. `sameSchoolCorroboration` is a
 * server-derived flag that is revalidated against canonical relationship data
 * for account matches. `expectedRole` lets an explicitly approved provisioning
 * workflow require a compatible persisted role; it defaults to the previously
 * supported parent role and never grants a role by itself.
 */
final readonly class IdentityMatchRequest
{
    public function __construct(
        public MatchableEntityType $entityType,
        public string $schoolId,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $dateOfBirth = null,
        public ?string $studentNumber = null,
        public ?string $admissionNumber = null,
        public ?string $guardianNumber = null,
        public int|string|null $guardianId = null,
        public int|string|null $userId = null,
        public int|string|null $studentId = null,
        public bool $sameSchoolCorroboration = false,
        public bool $embeddedGuardianReference = false,
        public ?string $expectedRole = null,
    ) {
    }
}
