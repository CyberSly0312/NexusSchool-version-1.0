<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::firstOrCreate(
            ['slug' => 'nexus-demo'],
            [
                'name' => 'Nexus Demo Academy',
                'database' => 'school_nexus_demo',
                'status' => 'active',
            ],
        );

        AcademicYear::firstOrCreate(
            ['school_id' => $school->id, 'name' => '2025-2026'],
            [
                'start_date' => '2025-10-01',
                'end_date' => '2026-07-31',
                'status' => 'active',
            ],
        );
    }
}
