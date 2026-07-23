<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\Driver;
use App\Models\FuelLog;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class FuelLogSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();
        if (! $school) {
            return;
        }

        $vehicles = Vehicle::where('school_id', $school->id)->get();
        $drivers = Driver::where('school_id', $school->id)->get();

        if ($vehicles->isEmpty() || $drivers->isEmpty()) {
            return;
        }

        $fuelLogs = [
            [
                'vehicle_id' => $vehicles[0]->id,
                'driver_id' => $drivers[0]->id,
                'fuel_date' => now()->subDays(10),
                'liters' => 45.50,
                'cost_per_liter' => 750,
                'total_cost' => 34125,
                'odometer_reading' => 45230,
                'fuel_type' => 'diesel',
                'fuel_station' => 'Total Ouagadougou',
                'notes' => 'Plein avant les trajets matinaux',
            ],
            [
                'vehicle_id' => $vehicles[1]->id,
                'driver_id' => $drivers[1]->id,
                'fuel_date' => now()->subDays(7),
                'liters' => 60.00,
                'cost_per_liter' => 750,
                'total_cost' => 45000,
                'odometer_reading' => 31450,
                'fuel_type' => 'diesel',
                'fuel_station' => 'Shell Bobo-Dioulasso',
                'notes' => 'Ravitaillement pour longue distance',
            ],
            [
                'vehicle_id' => $vehicles[2]->id,
                'driver_id' => $drivers[2]->id,
                'fuel_date' => now()->subDays(5),
                'liters' => 50.25,
                'cost_per_liter' => 755,
                'total_cost' => 37939,
                'odometer_reading' => 12800,
                'fuel_type' => 'diesel',
                'fuel_station' => 'Total Kossodo',
                'notes' => 'Ravitaillement hebdomadaire',
            ],
        ];

        foreach ($fuelLogs as $log) {
            FuelLog::firstOrCreate(
                ['school_id' => $school->id, 'vehicle_id' => $log['vehicle_id'], 'fuel_date' => $log['fuel_date']],
                $log
            );
        }
    }
}
