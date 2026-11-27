<?php

declare(strict_types=1);

namespace App\Domains\User\Policies;

use App\Domains\User\Enums\EnrollmentProvisioningKind;
use App\Models\UserInvitation;

/**
 * Narrow Task 11.2 provisioning policy.
 *
 * This is intentionally separate from SchoolAdminRolePolicy. It must never be
 * used by generic role assignment, Filament role management or ordinary staff
 * provisioning. The only roles reachable here are tied to a confirmed,
 * same-school enrollment workflow.
 */
final class EnrollmentLinkedProvisioningPolicy
{
    /**
     * @var array<string, string>
     */
    private const ROLE_BY_KIND = [
        EnrollmentProvisioningKind::Student->value => 'student',
        EnrollmentProvisioningKind::Parent->value => 'parent',
    ];

    /**
     * @var array<string, string>
     */
    private const PURPOSE_BY_KIND = [
        EnrollmentProvisioningKind::Student->value => UserInvitation::PURPOSE_STUDENT_INITIAL,
        EnrollmentProvisioningKind::Parent->value => UserInvitation::PURPOSE_GUARDIAN_INITIAL,
    ];

    public static function roleFor(EnrollmentProvisioningKind $kind): string
    {
        return self::ROLE_BY_KIND[$kind->value];
    }

    public static function purposeFor(EnrollmentProvisioningKind $kind): string
    {
        return self::PURPOSE_BY_KIND[$kind->value];
    }

    public static function allowsRoleFor(EnrollmentProvisioningKind $kind, string $roleName): bool
    {
        return self::roleFor($kind) === strtolower(trim($roleName));
    }
}
