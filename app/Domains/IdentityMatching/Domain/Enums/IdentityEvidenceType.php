<?php

declare(strict_types=1);

namespace App\Domains\IdentityMatching\Domain\Enums;

/**
 * Non-sensitive classifications attached to a match result.
 *
 * Evidence never carries raw email addresses, phone numbers, names, tokens or
 * passwords. It only describes which class of information was considered.
 */
enum IdentityEvidenceType: string
{
    case DurableSchoolIdentifier = 'DURABLE_SCHOOL_IDENTIFIER';
    case ExactNormalizedEmail = 'EXACT_NORMALIZED_EMAIL';
    case SameSchoolCorroboration = 'SAME_SCHOOL_CORROBORATION';
    case NameSimilarity = 'NAME_SIMILARITY';
    case DateOfBirthSupport = 'DATE_OF_BIRTH_SUPPORT';
    case PhoneSimilarity = 'PHONE_SIMILARITY';
    case EmbeddedGuardianReference = 'EMBEDDED_GUARDIAN_REFERENCE';
    case ConflictingIdentity = 'CONFLICTING_IDENTITY';
    case CrossSchoolConstraint = 'CROSS_SCHOOL_CONSTRAINT';
    case SoftDeletedRecord = 'SOFT_DELETED_RECORD';
    case IneligibleAccount = 'INELIGIBLE_ACCOUNT';
}
