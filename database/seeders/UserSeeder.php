<?php

namespace Database\Seeders;

use App\Domains\School\Enums\SchoolStatus;
use App\Domains\School\Models\School;
use App\Domains\User\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::firstOrCreate(
            ['slug' => 'nexus-demo'],
            [
                'name' => 'Nexus Demo Academy',
                'database' => 'school_nexus_demo',
                'email' => 'admin@nexusdemo.edu',
                'phone' => '+225 01 00 00 00',
                'address' => '123 Avenue de l\'Éducation',
                'city' => 'Ouagadougou',
                'state' => 'Centre',
                'country' => 'Burkina Faso',
                'timezone' => 'Africa/Ouagadougou',
                'currency' => 'XOF',
                'locale' => 'fr',
                'school_type' => 'secondary',
                'status' => SchoolStatus::Active,
            ]
        );

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

        if (!$admin->hasRole('super_admin')) {
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

        if (!$teacher->hasRole('teacher')) {
            $teacher->assignRole('teacher');
        }
    }
}
