<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Application\DTOs;

use App\Domains\Guardian\Domain\Enums\GuardianAccessDenialReason;
use App\Domains\Guardian\Domain\Exceptions\GuardianRelationshipVerificationException;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use ValueError;

/**
 * Trusted input for revoking one explicitly authorized child relationship.
 *
 * Only a tenant-resolved relationship ID and a stable reason code are accepted.
 * Raw school/user/guardian/student identifiers are rejected because ownership
 * and target records are always resolved by the application service.
 */
final readonly class GuardianRelationshipRevocationData
{
    private function __construct(
        public int $relationshipId,
        public GuardianAccessDenialReason $reason = GuardianAccessDenialReason::RelationshipRevoked,
    ) {
    }

    public static function fromRelationship(
        StudentGuardianRelationship $relationship,
        GuardianAccessDenialReason $reason = GuardianAccessDenialReason::RelationshipRevoked,
    ): self {
        return new self((int) $relationship->getKey(), $reason);
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

        $reason = $data['reason'] ?? GuardianAccessDenialReason::RelationshipRevoked;

        if (is_string($reason)) {
            try {
                $reason = GuardianAccessDenialReason::from($reason);
            } catch (ValueError) {
                throw GuardianRelationshipVerificationException::because(
                    GuardianAccessDenialReason::IdentifierOverrideRejected,
                );
            }
        }

        if (! $reason instanceof GuardianAccessDenialReason) {
            throw GuardianRelationshipVerificationException::because(
                GuardianAccessDenialReason::IdentifierOverrideRejected,
            );
        }

        return new self($relationshipId, $reason);
    }
}
