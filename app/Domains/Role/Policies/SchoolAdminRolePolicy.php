<?php

declare(strict_types=1);

namespace App\Domains\Role\Policies;

/**
 * Authoritative role allowlist for school-admin role changes.
 *
 * Task 08 provisioning and Task 10B existing-account role management must
 * share this single source of truth. Presentation layers may use the labels,
 * but every mutation is validated again by UserRoleChangeService.
 */
final class SchoolAdminRolePolicy
{
    /**
     * @var list<string>
     */
    public const ALLOWED_ROLES = [
        'teacher',
        'secretary',
        'accountant',
    ];

    /**
     * @var array<string, string>
     */
    public const LABELS = [
        'teacher' => 'Enseignant',
        'secretary' => 'Secrétaire',
        'accountant' => 'Comptable',
    ];

    public static function allows(string $roleName): bool
    {
        return in_array($roleName, self::ALLOWED_ROLES, true);
    }

    /**
     * @return array<string, string>
     */
    public static function allowedRolesWithLabels(): array
    {
        return array_intersect_key(
            self::LABELS,
            array_flip(self::ALLOWED_ROLES),
        );
    }

    public static function label(string $roleName): string
    {
        return self::LABELS[$roleName] ?? $roleName;
    }
}
