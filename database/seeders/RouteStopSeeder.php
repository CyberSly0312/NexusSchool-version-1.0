<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\Route;
use App\Models\RouteStop;
use Illuminate\Database\Seeder;

class RouteStopSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();
        if (! $school) {
            return;
        }

        $routes = Route::where('school_id', $school->id)->get();

        $stopNames = [
            ['Market Square', 'Hospital Junction', 'Town Hall'],
            ['Community Center', 'Police Station'],
            ['Industrial Area', 'School Gate', 'Church Road'],
            ['Traffic Light', 'Stadium', 'Library'],
            ['City Center', 'Fountain'],
        ];

        foreach ($routes as $index => $route) {
            $stops = $stopNames[$index] ?? [];

            foreach ($stops as $order => $stopName) {
                RouteStop::firstOrCreate(
                    ['school_id' => $school->id, 'route_id' => $route->id, 'stop_order' => $order + 1],
                    ['name' => $stopName]
                );
            }
        }
    }
}
