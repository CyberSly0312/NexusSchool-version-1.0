<?php

namespace Database\Seeders;

use App\Domains\School\Enums\SchoolStatus;
use App\Domains\School\Enums\SchoolType;
use App\Domains\School\Models\School;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        School::updateOrCreate(
            ['slug' => 'nexus-demo'],
            [
                'uuid' => Str::ulid()->toString(),
                'name' => 'Nexus Demo Academy',
                'database' => 'school_nexus_demo',
                'domain' => 'nexus-demo.nexus.school',
                'email' => 'admin@nexusdemo.nexus.school',
                'phone' => '+226 01 00 00 00',
                'address' => '123 Avenue de l\'Éducation',
                'city' => 'Ouagadougou',
                'state' => 'Centre',
                'country' => 'Burkina Faso',
                'postal_code' => '01000',
                'timezone' => 'Africa/Ouagadougou',
                'currency' => 'XOF',
                'locale' => 'fr',
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
