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
                'description' => 'Vidange et remplacement du filtre',
                'cost' => 75000.00,
                'status' => 'completed',
                'service_date' => now()->subMonth(),
                'completed_at' => now()->subMonth(),
                'service_provider' => 'Garage Central',
            ],
            [
                'type' => 'inspection',
                'description' => 'Inspection annuelle de sécurité',
                'cost' => 35000.00,
                'status' => 'completed',
                'service_date' => now()->subWeeks(2),
                'completed_at' => now()->subWeeks(2),
                'service_provider' => 'Centre Technique Auto',
            ],
            [
                'type' => 'corrective',
                'description' => 'Remplacement des plaquettes de frein',
                'cost' => 120000.00,
                'status' => 'scheduled',
                'service_date' => now()->addWeek(),
                'completed_at' => null,
                'service_provider' => 'Garage Central',
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
                    'service_date' => $maintenance['service_date'],
                    'completed_at' => $maintenance['completed_at'],
                    'service_provider' => $maintenance['service_provider'],
                ]
            );
        }
    }
}
