<?php

namespace Database\Seeders;

use App\Domains\Permission\Services\PermissionService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = (new PermissionService)->getDefaultPermissions();

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['name' => $permissionData['name'], 'guard_name' => $permissionData['guard_name']],
                $permissionData,
            );
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
