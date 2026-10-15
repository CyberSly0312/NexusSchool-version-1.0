<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        // SchoolSeeder owns demo-school provisioning and domain registration.
        $school = School::where('slug', 'nexus-demo')->firstOrFail();

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
