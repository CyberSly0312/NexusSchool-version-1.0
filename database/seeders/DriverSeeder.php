<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\Driver;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();
        if (! $school) {
            return;
        }

        $drivers = [
            ['first_name' => 'Kwame', 'last_name' => 'Mensah', 'phone' => '+233201234567', 'license_number' => 'DL-GH-00001', 'license_category' => 'C', 'status' => 'available'],
            ['first_name' => 'Ama', 'last_name' => 'Owusu', 'phone' => '+233202345678', 'license_number' => 'DL-GH-00002', 'license_category' => 'C', 'status' => 'available'],
            ['first_name' => 'Kofi', 'last_name' => 'Asare', 'phone' => '+233203456789', 'license_number' => 'DL-GH-00003', 'license_category' => 'D', 'status' => 'on_trip'],
            ['first_name' => 'Efua', 'last_name' => 'Boateng', 'phone' => '+233204567890', 'license_number' => 'DL-GH-00004', 'license_category' => 'C', 'status' => 'available'],
            ['first_name' => 'Yaw', 'last_name' => 'Adjei', 'phone' => '+233205678901', 'license_number' => 'DL-GH-00005', 'license_category' => 'D', 'status' => 'on_leave'],
        ];

        foreach ($drivers as $driver) {
            Driver::firstOrCreate(
                ['school_id' => $school->id, 'license_number' => $driver['license_number']],
                [
                    ...$driver,
                    'email' => strtolower($driver['first_name'][0].$driver['last_name']).'@email.com',
                    'license_expiry' => now()->addYears(3),
                    'date_of_birth' => '1985-01-15',
                ]
            );
        }
    }
}
