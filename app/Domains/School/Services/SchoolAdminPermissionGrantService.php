<?php

declare(strict_types=1);

namespace App\Domains\School\Services;

use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/**
 * Grants exactly the school-level permission set used by the legacy initial
 * School Admin setup flow.
 *
 * This mirrors the legacy service without changing it, so the historical
 * recovery path keeps its own behaviour. Grants are additive: deliberate extra
 * permissions on the role are preserved.
 */
final class SchoolAdminPermissionGrantService
{
    /**
     * @var list<string>
     */
    private const EXCLUDED_PERMISSIONS = [
        'school.create',
        'school.delete',
        'permission.create',
        'permission.update',
        'permission.delete',
        'audit_log.create',
        'audit_log.update',
        'audit_log.delete',
    ];

    /**
     * @var list<string>
     */
    private const EXCLUDED_PERMISSION_PREFIXES = [
        'api.',
        'backup.',
        'maintenance_window.',
    ];

    public function grant(RoleContract $role): void
    {
        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->get()
            ->reject(fn (Permission $permission): bool => $this->isPlatformOnlyPermission($permission->name))
            ->pluck('name')
            ->all();

        if ($permissions !== []) {
            $role->givePermissionTo($permissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function isPlatformOnlyPermission(string $name): bool
    {
        if (in_array($name, self::EXCLUDED_PERMISSIONS, true)) {
            return true;
        }

        foreach (self::EXCLUDED_PERMISSION_PREFIXES as $prefix) {
            if (str_starts_with($name, $prefix)) {
                return true;
            }
        }

        return false;
    }
}
