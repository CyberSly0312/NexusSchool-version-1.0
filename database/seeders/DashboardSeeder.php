<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\Dashboard;
use Illuminate\Database\Seeder;

class DashboardSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();

        if (! $school) {
            return;
        }

        $dashboards = [
            [
                'name' => 'School Overview',
                'slug' => 'school-overview',
                'description' => 'Main school dashboard with key metrics',
                'type' => 'school',
                'is_default' => true,
            ],
            [
                'name' => 'Academic Dashboard',
                'slug' => 'academic-dashboard',
                'description' => 'Academic performance and enrollment metrics',
                'type' => 'academic',
                'is_default' => false,
            ],
            [
                'name' => 'Finance Dashboard',
                'slug' => 'finance-dashboard',
                'description' => 'Financial overview and fee collection',
                'type' => 'finance',
                'is_default' => false,
            ],
            [
                'name' => 'Library Dashboard',
                'slug' => 'library-dashboard',
                'description' => 'Library usage and circulation stats',
                'type' => 'library',
                'is_default' => false,
            ],
            [
                'name' => 'Transport Dashboard',
                'slug' => 'transport-dashboard',
                'description' => 'Transport fleet and trip metrics',
                'type' => 'transport',
                'is_default' => false,
            ],
        ];

        foreach ($dashboards as $data) {
            Dashboard::firstOrCreate(
                ['school_id' => $school->id, 'slug' => $data['slug']],
                array_merge($data, ['school_id' => $school->id]),
            );
        }
    }
}
