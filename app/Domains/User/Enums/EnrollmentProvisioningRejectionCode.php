<?php

declare(strict_types=1);

namespace App\Domains\User\Enums;

/**
 * Safe reason codes for audited Task 11.2 provisioning decisions.
 *
 * Codes deliberately carry no email, phone, name, token, password or raw
 * evidence value.
 */
enum EnrollmentProvisioningRejectionCode: string
{
    case TenantContextMissing = 'TENANT_CONTEXT_MISSING';
    case TenantContextMismatch = 'TENANT_CONTEXT_MISMATCH';
    case TenantSchoolInactive = 'TENANT_SCHOOL_INACTIVE';
    case ActorNotAuthorized = 'ACTOR_NOT_AUTHORIZED';
    case ActorInactive = 'ACTOR_INACTIVE';
    case EnrollmentNotConfirmed = 'ENROLLMENT_NOT_CONFIRMED';
    case EnrollmentMismatch = 'ENROLLMENT_MISMATCH';
    case ExplicitRequestMissing = 'EXPLICIT_REQUEST_MISSING';
    case GuardianNotSelected = 'GUARDIAN_NOT_SELECTED';
    case GuardianNotEnrollmentLinked = 'GUARDIAN_NOT_ENROLLMENT_LINKED';
    case EligibilityMissing = 'STUDENT_ELIGIBILITY_MISSING';
    case ContactEvidenceMissing = 'STUDENT_CONTACT_EVIDENCE_MISSING';
    case IdentityPossible = 'IDENTITY_POSSIBLE_MATCH';
    case IdentityConflict = 'IDENTITY_CONFLICT';
    case IdentityCandidateMismatch = 'IDENTITY_CANDIDATE_MISMATCH';
    case EmailRequired = 'EMAIL_REQUIRED';
    case EmailUnavailable = 'EMAIL_UNAVAILABLE';
    case RoleUnavailable = 'ROLE_UNAVAILABLE';
    case InvitationConflict = 'INVITATION_CONFLICT';
    case ActivationBlocked = 'ACTIVATION_BLOCKED_PENDING_TASK_11_3';
}
