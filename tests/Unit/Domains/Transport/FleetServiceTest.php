<?php

namespace Tests\Unit\Domains\Transport;

use App\Domains\Transport\Rules\DriverRules;
use App\Domains\Transport\Rules\TripRules;
use App\Domains\Transport\Rules\VehicleRules;
use App\Domains\Transport\Services\FleetService;
use App\Models\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class FleetServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FleetService $service;

    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schoolId = 'test-school-'.uniqid();
        \DB::table('tenants')->insert([
            'id' => $this->schoolId,
            'uuid' => Str::ulid(),
            'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(),
            'database' => 'school_test_school',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->service = new FleetService(
            new VehicleRules,
            new DriverRules,
            new TripRules
        );
    }

    public function test_can_register_vehicle(): void
    {
        $data = [
            'school_id' => $this->schoolId,
            'registration_number' => 'GR-1234-23',
            'vin' => '1HGCM82633A123456',
            'manufacturer' => 'Toyota',
            'model' => 'HiAce',
            'year' => 2023,
            'capacity' => 14,
            'fuel_type' => 'diesel',
        ];

        $vehicle = $this->service->registerVehicle($data);

        $this->assertNotNull($vehicle);
        $this->assertDatabaseHas('vehicles', ['registration_number' => 'GR-1234-23', 'school_id' => $this->schoolId]);
    }

    public function test_can_update_vehicle(): void
    {
        $vehicle = $this->service->registerVehicle([
            'school_id' => $this->schoolId,
            'registration_number' => 'GR-1234-23',
            'vin' => '1HGCM82633A123456',
            'manufacturer' => 'Toyota',
            'model' => 'HiAce',
            'year' => 2023,
            'capacity' => 14,
            'fuel_type' => 'diesel',
        ]);

        $updated = $this->service->updateVehicle($vehicle->id, [
            'color' => 'White',
            'notes' => 'Updated vehicle details',
        ]);

        $this->assertDatabaseHas('vehicles', ['id' => $vehicle->id, 'color' => 'White']);
    }

    public function test_can_assign_driver(): void
    {
        $expiry = now()->addYears(3)->format('Y-m-d');
        $data = [
            'school_id' => $this->schoolId,
            'first_name' => 'Kwame',
            'last_name' => 'Mensah',
            'phone' => '+233201234567',
            'license_number' => 'DL-GH-00001',
            'license_expiry' => $expiry,
        ];

        $driver = $this->service->assignDriver($data);

        $this->assertNotNull($driver);
        $this->assertDatabaseHas('drivers', ['license_number' => 'DL-GH-00001', 'school_id' => $this->schoolId]);
    }

    public function test_can_update_driver(): void
    {
        $driver = $this->service->assignDriver([
            'school_id' => $this->schoolId,
            'first_name' => 'Kwame',
            'last_name' => 'Mensah',
            'phone' => '+233201234567',
            'license_number' => 'DL-GH-00001',
            'license_expiry' => now()->addYears(3)->format('Y-m-d'),
        ]);

        $updated = $this->service->updateDriver($driver->id, [
            'phone' => '+233209999999',
        ]);

        $this->assertDatabaseHas('drivers', ['id' => $driver->id, 'phone' => '+233209999999']);
    }

    public function test_can_schedule_trip(): void
    {
        $vehicle = $this->service->registerVehicle([
            'school_id' => $this->schoolId,
            'registration_number' => 'GR-1234-23',
            'vin' => '1HGCM82633A123456',
            'manufacturer' => 'Toyota',
            'model' => 'HiAce',
            'year' => 2023,
            'capacity' => 14,
            'fuel_type' => 'diesel',
        ]);

        $driver = $this->service->assignDriver([
            'school_id' => $this->schoolId,
            'first_name' => 'Kwame',
            'last_name' => 'Mensah',
            'phone' => '+233201234567',
            'license_number' => 'DL-GH-00001',
            'license_expiry' => now()->addYears(3)->format('Y-m-d'),
        ]);

        $route = Route::create([
            'school_id' => $this->schoolId,
            'name' => 'Route A',
            'status' => 'active',
        ]);

        $trip = $this->service->scheduleTrip([
            'school_id' => $this->schoolId,
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'route_id' => $route->id,
            'direction' => 'to_school',
            'academic_date' => now()->format('Y-m-d'),
        ]);

        $this->assertNotNull($trip);
        $this->assertDatabaseHas('trips', ['status' => 'scheduled', 'school_id' => $this->schoolId]);
    }

    public function test_can_start_trip(): void
    {
        $trip = $this->createTrip();

        $started = $this->service->startTrip($trip->id);

        $this->assertDatabaseHas('trips', ['id' => $trip->id, 'status' => 'started']);
    }

    public function test_can_complete_trip(): void
    {
        $trip = $this->createTrip();
        $this->service->startTrip($trip->id);

        $completed = $this->service->completeTrip($trip->id);

        $this->assertDatabaseHas('trips', ['id' => $trip->id, 'status' => 'completed']);
    }

    public function test_can_cancel_trip(): void
    {
        $trip = $this->createTrip();

        $this->service->cancelTrip($trip->id);

        $this->assertDatabaseHas('trips', ['id' => $trip->id, 'status' => 'cancelled']);
    }

    public function test_validates_vehicle_data(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->registerVehicle([]);
    }

    public function test_get_available_vehicles(): void
    {
        $this->service->registerVehicle([
            'school_id' => $this->schoolId,
            'registration_number' => 'GR-1111-23',
            'vin' => '1HGCM82633A111111',
            'manufacturer' => 'Toyota',
            'model' => 'HiAce',
            'year' => 2023,
            'capacity' => 14,
            'fuel_type' => 'diesel',
            'status' => 'available',
        ]);

        $this->service->registerVehicle([
            'school_id' => $this->schoolId,
            'registration_number' => 'GR-2222-23',
            'vin' => '1HGCM82633A222222',
            'manufacturer' => 'Hyundai',
            'model' => 'County',
            'year' => 2022,
            'capacity' => 25,
            'fuel_type' => 'diesel',
            'status' => 'maintenance',
        ]);

        $available = $this->service->getAvailableVehicles();

        $this->assertCount(1, $available);
        $this->assertEquals('GR-1111-23', $available->first()->registration_number);
    }

    protected function createTrip()
    {
        $vehicle = $this->service->registerVehicle([
            'school_id' => $this->schoolId,
            'registration_number' => 'GR-TRIP-23',
            'vin' => '1HGCM82633A333333',
            'manufacturer' => 'Toyota',
            'model' => 'HiAce',
            'year' => 2023,
            'capacity' => 14,
            'fuel_type' => 'diesel',
        ]);

        $driver = $this->service->assignDriver([
            'school_id' => $this->schoolId,
            'first_name' => 'Test',
            'last_name' => 'Driver',
            'phone' => '+233200000000',
            'license_number' => 'DL-TRIP-001',
            'license_expiry' => now()->addYears(3)->format('Y-m-d'),
        ]);

        $route = Route::create([
            'school_id' => $this->schoolId,
            'name' => 'Test Route',
            'status' => 'active',
        ]);

        return $this->service->scheduleTrip([
            'school_id' => $this->schoolId,
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'route_id' => $route->id,
            'direction' => 'to_school',
            'academic_date' => now()->format('Y-m-d'),
        ]);
    }
}
