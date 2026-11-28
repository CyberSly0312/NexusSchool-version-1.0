<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Application\DTOs;

use App\Domains\Guardian\Domain\Enums\GuardianAccessDenialReason;
use App\Domains\Guardian\Domain\Exceptions\GuardianRelationshipVerificationException;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;

/**
 * Trusted input for one durable Guardian–Student relationship decision.
 *
 * The only selector is a relationship ID resolved from a tenant-scoped record.
 * Caller-supplied school, user, guardian and student identifiers are forbidden:
 * identity evidence and relationship ownership are resolved by the application
 * service from tenant context and the persisted relationship itself.
 */
final readonly class GuardianRelationshipVerificationData
{
    private function __construct(
        public int $relationshipId,
    ) {
    }

    public static function fromRelationship(StudentGuardianRelationship $relationship): self
    {
        return new self((int) $relationship->getKey());
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $forbidden = array_intersect(
            ['school_id', 'user_id', 'guardian_id', 'student_id'],
            array_keys($data),
        );

        if ($forbidden !== []) {
            throw GuardianRelationshipVerificationException::because(
                GuardianAccessDenialReason::IdentifierOverrideRejected,
            );
        }

        $relationshipId = $data['relationship_id'] ?? null;

        if (is_string($relationshipId) && ctype_digit($relationshipId)) {
            $relationshipId = (int) $relationshipId;
        }

        if (! is_int($relationshipId) || $relationshipId <= 0) {
            throw GuardianRelationshipVerificationException::because(
                GuardianAccessDenialReason::IdentifierOverrideRejected,
            );
        }

        return new self($relationshipId);
    }
}
