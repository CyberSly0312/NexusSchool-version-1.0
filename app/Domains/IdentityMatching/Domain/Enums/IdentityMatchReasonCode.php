<?php

declare(strict_types=1);

namespace App\Domains\IdentityMatching\Domain\Enums;

/**
 * Internal reason codes for deterministic matching decisions.
 *
 * These codes are safe for audited application logs. They are classifications,
 * not personal data, and never identify a candidate in another school.
 */
enum IdentityMatchReasonCode: string
{
    case TenantContextMissing = 'TENANT_CONTEXT_MISSING';
    case TenantContextMismatch = 'TENANT_CONTEXT_MISMATCH';
    case TenantSchoolInactive = 'TENANT_SCHOOL_INACTIVE';
    case NoCandidate = 'NO_CANDIDATE';
    case DurableIdentifierMatch = 'DURABLE_IDENTIFIER_MATCH';
    case ExactEmailGuardianMatch = 'EXACT_EMAIL_GUARDIAN_MATCH';
    case ExactEmailWithCorroboration = 'EXACT_EMAIL_WITH_CORROBORATION';
    case ExactEmailWithoutCorroboration = 'EXACT_EMAIL_WITHOUT_CORROBORATION';
    case NameOnly = 'NAME_ONLY';
    case NameAndDateOfBirthOnly = 'NAME_AND_DATE_OF_BIRTH_ONLY';
    case PhoneOnly = 'PHONE_ONLY';
    case EmbeddedReferenceOnly = 'EMBEDDED_REFERENCE_ONLY';
    case MultipleCandidates = 'MULTIPLE_CANDIDATES';
    case ConflictingEvidence = 'CONFLICTING_EVIDENCE';
    case CrossSchoolEmailConflict = 'CROSS_SCHOOL_EMAIL_CONFLICT';
    case SoftDeletedCandidate = 'SOFT_DELETED_CANDIDATE';
    case IneligibleAccount = 'INELIGIBLE_ACCOUNT';
    case RoleNotCompatible = 'ROLE_NOT_COMPATIBLE';
}
