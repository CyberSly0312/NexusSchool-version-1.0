<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Services;

use App\Domains\Plugin\DTOs\PluginManifest;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

final class PluginPermissionRegistration
{
    public function registerPermissions(PluginManifest $manifest): void
    {
        $permissions = $manifest->permissions;

        foreach ($permissions as $permissionData) {
            if (is_string($permissionData)) {
                $this->createPermission($permissionData, $manifest->name);
            } elseif (is_array($permissionData)) {
                $name = $permissionData['name'] ?? '';
                $guard = $permissionData['guard'] ?? 'web';
                $this->createPermission($name, $manifest->name, $guard);
            }
        }
    }

    public function unregisterPermissions(string $pluginName): void
    {
        $plugin = \App\Models\Plugin::where('name', $pluginName)->first();
        if ($plugin === null) {
            return;
        }

        $metadata = $plugin->metadata ?? [];
        $permissions = $metadata['permissions'] ?? [];

        foreach ($permissions as $permissionData) {
            $name = is_string($permissionData) ? $permissionData : ($permissionData['name'] ?? '');
            if ($name !== '') {
                Permission::where('name', $name)->delete();
            }
        }
    }

    private function createPermission(string $name, string $pluginName, string $guard = 'web'): void
    {
        Permission::firstOrCreate([
            'name' => $name,
            'guard_name' => $guard,
        ]);
    }
}
