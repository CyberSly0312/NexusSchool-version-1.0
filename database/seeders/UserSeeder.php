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
                'phone' => '+233 24 000 0000',
                'address' => '123 Education Lane',
                'city' => 'Accra',
                'state' => 'Greater Accra',
                'country' => 'Ghana',
                'timezone' => 'Africa/Accra',
                'currency' => 'GHS',
                'locale' => 'en',
                'school_type' => 'secondary',
                'status' => SchoolStatus::Active,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@nexusdemo.edu'],
            [
                'school_id' => $school->id,
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => Hash::make('password'),
                'status' => UserStatus::Active,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'teacher@nexusdemo.edu'],
            [
                'school_id' => $school->id,
                'first_name' => 'John',
                'last_name' => 'Doe',
                'password' => Hash::make('password'),
                'status' => UserStatus::Active,
                'email_verified_at' => now(),
            ]
        );
    }
}
