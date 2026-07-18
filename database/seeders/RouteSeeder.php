<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\Route;
use Illuminate\Database\Seeder;

class RouteSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();
        if (! $school) {
            return;
        }

        $routes = [
            ['name' => 'Route A - North Circle', 'description' => 'Covers the northern residential area', 'distance_km' => 12.50, 'estimated_duration_minutes' => 45, 'fare' => 200.00, 'status' => 'active'],
            ['name' => 'Route B - South Express', 'description' => 'Direct route to southern suburbs', 'distance_km' => 8.75, 'estimated_duration_minutes' => 30, 'fare' => 150.00, 'status' => 'active'],
            ['name' => 'Route C - East Corridor', 'description' => 'Covers eastern industrial and residential zones', 'distance_km' => 15.20, 'estimated_duration_minutes' => 55, 'fare' => 250.00, 'status' => 'active'],
            ['name' => 'Route D - West Link', 'description' => 'Western route through market areas', 'distance_km' => 10.00, 'estimated_duration_minutes' => 40, 'fare' => 175.00, 'status' => 'active'],
            ['name' => 'Route E - Central Loop', 'description' => 'Short central district loop', 'distance_km' => 5.30, 'estimated_duration_minutes' => 20, 'fare' => 100.00, 'status' => 'inactive'],
        ];

        foreach ($routes as $route) {
            Route::firstOrCreate(
                ['school_id' => $school->id, 'name' => $route['name']],
                $route
            );
        }
    }
}
