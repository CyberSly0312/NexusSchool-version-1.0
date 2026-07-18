<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\Kpi;
use Illuminate\Database\Seeder;

class KpiSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();

        if (! $school) {
            return;
        }

        $kpis = [
            [
                'name' => 'Student Enrollment',
                'slug' => 'student-enrollment',
                'description' => 'Total active student enrollments',
                'category' => 'enrollment',
                'target_value' => 500,
                'unit' => 'students',
                'calculation_frequency' => 'daily',
            ],
            [
                'name' => 'Attendance Rate',
                'slug' => 'attendance-rate',
                'description' => 'Daily attendance rate percentage',
                'category' => 'attendance',
                'target_value' => 95.00,
                'unit' => '%',
                'calculation_frequency' => 'daily',
            ],
            [
                'name' => 'Fee Collection Rate',
                'slug' => 'fee-collection-rate',
                'description' => 'Percentage of fees collected',
                'category' => 'finance',
                'target_value' => 90.00,
                'unit' => '%',
                'calculation_frequency' => 'monthly',
            ],
            [
                'name' => 'Library Utilization',
                'slug' => 'library-utilization',
                'description' => 'Number of active library loans',
                'category' => 'library',
                'target_value' => 100,
                'unit' => 'loans',
                'calculation_frequency' => 'weekly',
            ],
            [
                'name' => 'Transport Usage',
                'slug' => 'transport-usage',
                'description' => 'Daily transport trip completion rate',
                'category' => 'transport',
                'target_value' => 98.00,
                'unit' => '%',
                'calculation_frequency' => 'daily',
            ],
        ];

        foreach ($kpis as $data) {
            Kpi::firstOrCreate(
                ['school_id' => $school->id, 'slug' => $data['slug']],
                array_merge($data, ['school_id' => $school->id]),
            );
        }
    }
}
