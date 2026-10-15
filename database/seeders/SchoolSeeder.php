<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\School\DTOs\SchoolData;
use App\Domains\School\Enums\SchoolStatus;
use App\Domains\School\Enums\SchoolType;
use App\Domains\School\Models\School;
use App\Domains\School\Services\SchoolService;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        $attributes = [
                'name' => 'Nexus Demo Academy',
                'database' => 'school_nexus_demo',
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
            ];

        $school = School::where('slug', 'nexus-demo')->first();

        if (! $school instanceof School) {
            app(SchoolService::class)->create(SchoolData::fromArray([
                ...$attributes,
                'slug' => 'nexus-demo',
            ]));

            return;
        }

        $school->update($attributes);
    }
}
