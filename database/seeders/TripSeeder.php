<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\Driver;
use App\Models\Route;
use App\Models\Trip;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class TripSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();
        if (! $school) {
            return;
        }

        $vehicles = Vehicle::where('school_id', $school->id)->where('status', 'available')->get();
        $drivers = Driver::where('school_id', $school->id)->where('status', 'available')->get();
        $routes = Route::where('school_id', $school->id)->where('status', 'active')->get();

        if ($vehicles->isEmpty() || $drivers->isEmpty() || $routes->isEmpty()) {
            return;
        }

        $trips = [
            [
                'vehicle_id' => $vehicles[0]->id,
                'driver_id' => $drivers[0]->id,
                'route_id' => $routes[0]->id,
                'trip_type' => 'morning',
                'status' => 'completed',
                'departure_time' => now()->subDay()->setTime(6, 30),
                'arrival_time' => now()->subDay()->setTime(7, 15),
                'students_picked' => 12,
                'students_dropped' => 0,
            ],
            [
                'vehicle_id' => $vehicles[0]->id,
                'driver_id' => $drivers[0]->id,
                'route_id' => $routes[0]->id,
                'trip_type' => 'afternoon',
                'status' => 'completed',
                'departure_time' => now()->subDay()->setTime(15, 0),
                'arrival_time' => now()->subDay()->setTime(15, 45),
                'students_picked' => 0,
                'students_dropped' => 12,
            ],
            [
                'vehicle_id' => $vehicles[1]->id,
                'driver_id' => $drivers[1]->id,
                'route_id' => $routes[1]->id,
                'trip_type' => 'morning',
                'status' => 'scheduled',
                'departure_time' => now()->addDay()->setTime(6, 30),
                'arrival_time' => null,
                'students_picked' => 0,
                'students_dropped' => 0,
            ],
            [
                'vehicle_id' => $drivers->count() > 3 && $vehicles->count() > 2 ? $vehicles[2]->id : $vehicles[0]->id,
                'driver_id' => $drivers->count() > 3 ? $drivers[3]->id : $drivers[1]->id,
                'route_id' => $routes->count() > 2 ? $routes[2]->id : $routes[0]->id,
                'trip_type' => 'morning',
                'status' => 'in_progress',
                'departure_time' => now()->setTime(6, 30),
                'arrival_time' => null,
                'students_picked' => 5,
                'students_dropped' => 0,
            ],
        ];

        foreach ($trips as $trip) {
            Trip::firstOrCreate(
                ['school_id' => $school->id, 'vehicle_id' => $trip['vehicle_id'], 'route_id' => $trip['route_id'], 'departure_time' => $trip['departure_time']],
                $trip
            );
        }
    }
}
