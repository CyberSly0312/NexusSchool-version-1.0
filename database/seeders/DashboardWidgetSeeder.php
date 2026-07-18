<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\Dashboard;
use App\Models\DashboardWidget;
use Illuminate\Database\Seeder;

class DashboardWidgetSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();

        if (! $school) {
            return;
        }

        $dashboard = Dashboard::where('school_id', $school->id)->where('slug', 'school-overview')->first();

        if (! $dashboard) {
            return;
        }

        $widgets = [
            ['title' => 'Total Students', 'widget_type' => 'metric', 'data_source' => 'enrollment_stats', 'position' => 0],
            ['title' => 'Attendance Rate', 'widget_type' => 'metric', 'data_source' => 'attendance_stats', 'position' => 1],
            ['title' => 'Fee Collection', 'widget_type' => 'metric', 'data_source' => 'finance_stats', 'position' => 2],
            ['title' => 'Enrollment Trend', 'widget_type' => 'chart', 'data_source' => 'enrollment_stats', 'position' => 3],
            ['title' => 'Recent Announcements', 'widget_type' => 'list', 'data_source' => 'communication_stats', 'position' => 4],
        ];

        foreach ($widgets as $data) {
            DashboardWidget::firstOrCreate(
                ['dashboard_id' => $dashboard->id, 'title' => $data['title']],
                array_merge($data, [
                    'school_id' => $school->id,
                    'dashboard_id' => $dashboard->id,
                ]),
            );
        }
    }
}
