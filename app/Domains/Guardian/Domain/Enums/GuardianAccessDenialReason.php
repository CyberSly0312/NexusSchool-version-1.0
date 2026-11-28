<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Domain\Enums;

/**
 * Stable, non-sensitive reason codes for relationship and child-access denials.
 *
 * Codes are safe for audits and tests: they never carry names, emails, phone
 * numbers, tokens, documents or any other identity evidence.
 */
enum GuardianAccessDenialReason: string
{
    case TenantContextMissing = 'TENANT_CONTEXT_MISSING';
    case TenantContextMismatch = 'TENANT_CONTEXT_MISMATCH';
    case SchoolInactive = 'SCHOOL_INACTIVE';
    case UserNotFound = 'USER_NOT_FOUND';
    case UserNotActive = 'USER_NOT_ACTIVE';
    case ActorNotAuthenticated = 'ACTOR_NOT_AUTHENTICATED';
    case ActorMismatch = 'ACTOR_MISMATCH';
    case ActorNotActive = 'ACTOR_NOT_ACTIVE';
    case ActorNotAuthorized = 'ACTOR_NOT_AUTHORIZED';
    case ActorSchoolMismatch = 'ACTOR_SCHOOL_MISMATCH';
    case ActorTargetMismatch = 'ACTOR_TARGET_MISMATCH';
    case ParentRoleMissing = 'PARENT_ROLE_MISSING';
    case IdentifierOverrideRejected = 'IDENTIFIER_OVERRIDE_REJECTED';
    case GuardianAccountMissing = 'GUARDIAN_ACCOUNT_MISSING';
    case GuardianAccountMismatch = 'GUARDIAN_ACCOUNT_MISMATCH';
    case GuardianSchoolMismatch = 'GUARDIAN_SCHOOL_MISMATCH';
    case GuardianInactive = 'GUARDIAN_INACTIVE';
    case GuardianAccountAmbiguous = 'GUARDIAN_ACCOUNT_AMBIGUOUS';
    case StudentNotFound = 'STUDENT_NOT_FOUND';
    case StudentSchoolMismatch = 'STUDENT_SCHOOL_MISMATCH';
    case IdentityMatchRejected = 'IDENTITY_MATCH_REJECTED';
    case IdentityMatchCandidateMismatch = 'IDENTITY_MATCH_CANDIDATE_MISMATCH';
    case RelationshipNotFound = 'RELATIONSHIP_NOT_FOUND';
    case RelationshipSchoolMismatch = 'RELATIONSHIP_SCHOOL_MISMATCH';
    case RelationshipGuardianMismatch = 'RELATIONSHIP_GUARDIAN_MISMATCH';
    case RelationshipStudentMismatch = 'RELATIONSHIP_STUDENT_MISMATCH';
    case RelationshipConflict = 'RELATIONSHIP_CONFLICT';
    case RelationshipStateConflict = 'RELATIONSHIP_STATE_CONFLICT';
    case RelationshipRequestMissing = 'RELATIONSHIP_REQUEST_MISSING';
    case RelationshipProvenanceMissing = 'RELATIONSHIP_PROVENANCE_MISSING';
    case RelationshipUnverified = 'RELATIONSHIP_UNVERIFIED';
    case RelationshipNotAuthorized = 'RELATIONSHIP_NOT_AUTHORIZED';
    case RelationshipRevoked = 'RELATIONSHIP_REVOKED';
    case RelationshipNotStarted = 'RELATIONSHIP_NOT_STARTED';
    case RelationshipExpired = 'RELATIONSHIP_EXPIRED';
    case RelationshipTypeUnavailable = 'RELATIONSHIP_TYPE_UNAVAILABLE';
    case OperationNotPermitted = 'OPERATION_NOT_PERMITTED';
}
