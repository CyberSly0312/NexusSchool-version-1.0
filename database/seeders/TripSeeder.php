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
                'direction' => 'outbound',
                'academic_date' => now()->subDay()->format('Y-m-d'),
                'status' => 'completed',
                'departure_time' => now()->subDay()->setTime(6, 30),
                'arrival_time' => now()->subDay()->setTime(7, 15),
                'actual_departure' => now()->subDay()->setTime(6, 32),
                'actual_arrival' => now()->subDay()->setTime(7, 12),
                'notes' => 'Trajet matinal effectué',
            ],
            [
                'vehicle_id' => $vehicles[0]->id,
                'driver_id' => $drivers[0]->id,
                'route_id' => $routes[0]->id,
                'direction' => 'inbound',
                'academic_date' => now()->subDay()->format('Y-m-d'),
                'status' => 'completed',
                'departure_time' => now()->subDay()->setTime(15, 0),
                'arrival_time' => now()->subDay()->setTime(15, 45),
                'actual_departure' => now()->subDay()->setTime(15, 2),
                'actual_arrival' => now()->subDay()->setTime(15, 42),
                'notes' => 'Retour après-midi effectué',
            ],
            [
                'vehicle_id' => $vehicles[1]->id,
                'driver_id' => $drivers[1]->id,
                'route_id' => $routes[1]->id,
                'direction' => 'outbound',
                'academic_date' => now()->addDay()->format('Y-m-d'),
                'status' => 'scheduled',
                'departure_time' => now()->addDay()->setTime(6, 30),
                'arrival_time' => null,
                'notes' => 'Trajet planifié pour demain',
            ],
            [
                'vehicle_id' => $vehicles->count() > 2 ? $vehicles[2]->id : $vehicles[0]->id,
                'driver_id' => $drivers->count() > 1 ? $drivers[1]->id : $drivers[0]->id,
                'route_id' => $routes->count() > 1 ? $routes[1]->id : $routes[0]->id,
                'direction' => 'outbound',
                'academic_date' => now()->format('Y-m-d'),
                'status' => 'started',
                'departure_time' => now()->setTime(6, 30),
                'arrival_time' => null,
                'notes' => 'Trajet en cours',
            ],
        ];

        foreach ($trips as $trip) {
            Trip::firstOrCreate(
                ['school_id' => $school->id, 'vehicle_id' => $trip['vehicle_id'], 'route_id' => $trip['route_id'], 'direction' => $trip['direction'], 'departure_time' => $trip['departure_time']],
                $trip
            );
        }
    }
}
