<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Domains\User\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // SchoolSeeder owns demo-school provisioning through SchoolService.
        // This seeder must not create a tenant outside that transactional path.
        $school = School::where('slug', 'nexus-demo')->firstOrFail();

        $admin = User::updateOrCreate(
            ['email' => 'admin@nexusschool.local'],
            [
                'school_id' => $school->id,
                'first_name' => 'Admin',
                'last_name' => 'Système',
                'password' => Hash::make('admin1234'),
                'status' => UserStatus::Active,
                'email_verified_at' => now(),
            ]
        );

        if (! $admin->hasRole('super_admin')) {
            $admin->assignRole('super_admin');
        }

        $teacher = User::updateOrCreate(
            ['email' => 'teacher@nexusdemo.edu'],
            [
                'school_id' => $school->id,
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'password' => Hash::make('password'),
                'status' => UserStatus::Active,
                'email_verified_at' => now(),
            ]
        );

        if (! $teacher->hasRole('teacher')) {
            $teacher->assignRole('teacher');
        }
    }
}
