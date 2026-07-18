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
                'date' => now()->subDays(10),
                'gallons' => 45.50,
                'cost_per_gallon' => 5.80,
                'total_cost' => 263.90,
                'odometer_reading' => 45230,
                'fuel_type' => 'diesel',
                'gas_station' => 'Shell Kantamanto',
                'notes' => 'Full tank refuel before morning routes',
            ],
            [
                'vehicle_id' => $vehicles[1]->id,
                'driver_id' => $drivers[1]->id,
                'date' => now()->subDays(7),
                'gallons' => 60.00,
                'cost_per_gallon' => 5.80,
                'total_cost' => 348.00,
                'odometer_reading' => 31450,
                'fuel_type' => 'diesel',
                'gas_station' => 'Goil Ring Road',
                'notes' => 'Top-up for long route coverage',
            ],
            [
                'vehicle_id' => $vehicles[2]->id,
                'driver_id' => $drivers[2]->id,
                'date' => now()->subDays(5),
                'gallons' => 50.25,
                'cost_per_gallon' => 5.90,
                'total_cost' => 296.48,
                'odometer_reading' => 12800,
                'fuel_type' => 'diesel',
                'gas_station' => 'Total Lapaz',
                'notes' => 'Routine mid-week refueling',
            ],
            [
                'vehicle_id' => $vehicles[0]->id,
                'driver_id' => $drivers[3]->id,
                'date' => now()->subDays(3),
                'gallons' => 42.00,
                'cost_per_gallon' => 5.85,
                'total_cost' => 245.70,
                'odometer_reading' => 45580,
                'fuel_type' => 'diesel',
                'gas_station' => 'Star Oil Airport',
                'notes' => 'Refuel after extended route assignment',
            ],
            [
                'vehicle_id' => $vehicles[3]->id,
                'driver_id' => $drivers[0]->id,
                'date' => now()->subDays(1),
                'gallons' => 70.00,
                'cost_per_gallon' => 5.85,
                'total_cost' => 409.50,
                'odometer_reading' => 58120,
                'fuel_type' => 'diesel',
                'gas_station' => 'Engen Tema',
                'notes' => 'Full tank after maintenance completion',
            ],
        ];

        foreach ($fuelLogs as $log) {
            FuelLog::firstOrCreate(
                ['school_id' => $school->id, 'vehicle_id' => $log['vehicle_id'], 'date' => $log['date']],
                $log
            );
        }
    }
}
