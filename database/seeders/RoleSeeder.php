<?php

namespace Database\Seeders;

use App\Domains\Role\Services\RoleService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = (new RoleService)->getDefaultRoles();

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name'], 'guard_name' => $roleData['guard_name']],
                $roleData,
            );
        }
    }
}
