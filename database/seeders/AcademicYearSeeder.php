<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        $school = \App\Models\School::firstOrCreate(
            ['domain' => 'default.localhost'],
            ['name' => 'Default School', 'status' => 'active']
        );

        AcademicYear::create([
            'school_id' => $school->id,
            'name' => '2025-2026',
            'start_date' => '2025-10-01',
            'end_date' => '2026-07-31',
            'status' => 'active',
        ]);
    }
}