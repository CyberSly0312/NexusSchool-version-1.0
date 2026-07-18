<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use Illuminate\Database\Seeder;

class VehicleMaintenanceSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();
        if (! $school) {
            return;
        }

        $vehicles = Vehicle::where('school_id', $school->id)->get();

        if ($vehicles->isEmpty()) {
            return;
        }

        $maintenanceData = [
            [
                'type' => 'preventive',
                'description' => 'Regular oil change and filter replacement',
                'cost' => 150.00,
                'status' => 'completed',
                'scheduled_date' => now()->subMonth(),
                'completed_date' => now()->subMonth(),
            ],
            [
                'type' => 'inspection',
                'description' => 'Annual safety inspection',
                'cost' => 75.00,
                'status' => 'completed',
                'scheduled_date' => now()->subWeeks(2),
                'completed_date' => now()->subWeeks(2),
            ],
            [
                'type' => 'corrective',
                'description' => 'Brake pad replacement',
                'cost' => 450.00,
                'status' => 'scheduled',
                'scheduled_date' => now()->addWeek(),
                'completed_date' => null,
            ],
        ];

        foreach ($maintenanceData as $index => $maintenance) {
            $vehicle = $vehicles[$index % $vehicles->count()];

            VehicleMaintenance::firstOrCreate(
                ['school_id' => $school->id, 'vehicle_id' => $vehicle->id, 'description' => $maintenance['description']],
                [
                    'type' => $maintenance['type'],
                    'cost' => $maintenance['cost'],
                    'status' => $maintenance['status'],
                    'scheduled_date' => $maintenance['scheduled_date'],
                    'completed_date' => $maintenance['completed_date'],
                ]
            );
        }
    }
}
