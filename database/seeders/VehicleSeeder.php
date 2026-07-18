<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();
        if (! $school) {
            return;
        }

        $vehicles = [
            [
                'registration_number' => 'GR-1234-23',
                'vin' => '1HGCM82633A123456',
                'manufacturer' => 'Toyota',
                'model' => 'HiAce',
                'year' => 2023,
                'color' => 'White',
                'capacity' => 14,
                'fuel_type' => 'diesel',
                'status' => 'available',
            ],
            [
                'registration_number' => 'GR-5678-23',
                'vin' => '2HGBH4J5XFM123456',
                'manufacturer' => 'Hyundai',
                'model' => 'County',
                'year' => 2022,
                'color' => 'Blue',
                'capacity' => 25,
                'fuel_type' => 'diesel',
                'status' => 'available',
            ],
            [
                'registration_number' => 'GR-9012-23',
                'vin' => '3NPECAJ01FM123456',
                'manufacturer' => 'Nissan',
                'model' => 'Civilian',
                'year' => 2024,
                'color' => 'Silver',
                'capacity' => 18,
                'fuel_type' => 'diesel',
                'status' => 'in_service',
            ],
            [
                'registration_number' => 'GR-3456-23',
                'vin' => '4T1BG1FK5FU123456',
                'manufacturer' => 'Toyota',
                'model' => 'Coaster',
                'year' => 2021,
                'color' => 'Yellow',
                'capacity' => 30,
                'fuel_type' => 'diesel',
                'status' => 'maintenance',
            ],
            [
                'registration_number' => 'GR-7890-23',
                'vin' => '5YJSA1E14HF123456',
                'manufacturer' => 'Mercedes-Benz',
                'model' => 'Sprinter',
                'year' => 2025,
                'color' => 'White',
                'capacity' => 20,
                'fuel_type' => 'diesel',
                'status' => 'available',
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::firstOrCreate(
                ['school_id' => $school->id, 'registration_number' => $vehicle['registration_number']],
                $vehicle
            );
        }
    }
}
