<?php

namespace Database\Seeders;

use App\Domains\School\Enums\SchoolStatus;
use App\Domains\School\Enums\SchoolType;
use App\Domains\School\Models\School;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        School::updateOrCreate(
            ['slug' => 'nexus-demo'],
            [
                'name' => 'Nexus Demo Academy',
                'database' => 'school_nexus_demo',
                'domain' => 'nexus-demo.nexus.school',
                'email' => 'admin@nexusdemo.nexus.school',
                'phone' => '+233 24 000 0000',
                'address' => '123 Education Lane',
                'city' => 'Accra',
                'state' => 'Greater Accra',
                'country' => 'Ghana',
                'postal_code' => '00233',
                'timezone' => 'Africa/Accra',
                'currency' => 'GHS',
                'locale' => 'en',
                'registration_number' => 'REG-00001',
                'school_type' => SchoolType::Secondary,
                'status' => SchoolStatus::Active,
                'settings' => [
                    'grading_system' => 'standard',
                    'max_students_per_class' => 40,
                    'academic_week_starts' => 'monday',
                ],
            ],
        );
    }
}
