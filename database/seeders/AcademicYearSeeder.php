<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        AcademicYear::create([
            'school_id' => 1,
            'name' => '2025-2026',
            'start_date' => '2025-10-01',
            'end_date' => '2026-07-31',
            'status' => 'active',
        ]);
    }
}