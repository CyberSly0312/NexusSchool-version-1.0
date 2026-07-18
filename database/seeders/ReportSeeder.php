<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\Report;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();

        if (! $school) {
            return;
        }

        $reports = [
            [
                'name' => 'Monthly Enrollment Report',
                'slug' => 'monthly-enrollment-report',
                'description' => 'Monthly student enrollment summary',
                'type' => 'enrollment',
                'format' => 'pdf',
                'status' => 'scheduled',
                'scheduled_frequency' => 'monthly',
            ],
            [
                'name' => 'Attendance Summary',
                'slug' => 'attendance-summary',
                'description' => 'Weekly attendance summary for all classes',
                'type' => 'attendance',
                'format' => 'excel',
                'status' => 'draft',
            ],
            [
                'name' => 'Library Usage Report',
                'slug' => 'library-usage-report',
                'description' => 'Monthly library circulation statistics',
                'type' => 'library',
                'format' => 'pdf',
                'status' => 'draft',
            ],
        ];

        foreach ($reports as $data) {
            Report::firstOrCreate(
                ['school_id' => $school->id, 'slug' => $data['slug']],
                array_merge($data, ['school_id' => $school->id]),
            );
        }
    }
}
