<?php

namespace Tests\Unit\Domains\Transport;

use App\Domains\Transport\Rules\DriverRules;
use App\Domains\Transport\Rules\FuelLogRules;
use App\Domains\Transport\Rules\TripRules;
use App\Domains\Transport\Rules\VehicleMaintenanceRules;
use App\Domains\Transport\Rules\VehicleRules;
use App\Domains\Transport\Services\FleetService;
use App\Domains\Transport\Services\MaintenanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MaintenanceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected MaintenanceService $service;

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

        $this->service = new MaintenanceService(
            new VehicleMaintenanceRules,
            new FuelLogRules
        );
    }

    public function test_can_schedule_maintenance(): void
    {
        $vehicleId = $this->createVehicle();

        $maintenance = $this->service->scheduleMaintenance([
            'school_id' => $this->schoolId,
            'vehicle_id' => $vehicleId,
            'type' => 'preventive',
            'description' => 'Regular oil change',
            'service_date' => now()->format('Y-m-d'),
        ]);

        $this->assertNotNull($maintenance);
        $this->assertDatabaseHas('vehicle_maintenances', ['vehicle_id' => $vehicleId, 'type' => 'preventive']);
    }

    public function test_can_complete_maintenance(): void
    {
        $vehicleId = $this->createVehicle();

        $maintenance = $this->service->scheduleMaintenance([
            'school_id' => $this->schoolId,
            'vehicle_id' => $vehicleId,
            'type' => 'preventive',
            'description' => 'Regular oil change',
            'service_date' => now()->format('Y-m-d'),
        ]);

        $completed = $this->service->completeMaintenance($maintenance->id, [
            'cost' => 150.00,
        ]);

        $this->assertDatabaseHas('vehicle_maintenances', ['id' => $maintenance->id, 'status' => 'completed']);
    }

    public function test_can_log_fuel(): void
    {
        $vehicleId = $this->createVehicle();

        $fuelLog = $this->service->logFuel([
            'school_id' => $this->schoolId,
            'vehicle_id' => $vehicleId,
            'liters' => 50,
            'cost_per_liter' => 2.50,
            'total_cost' => 125.00,
            'fuel_type' => 'diesel',
            'fuel_station' => 'Total Station',
            'odometer_reading' => 45000,
            'fuel_date' => now()->format('Y-m-d'),
        ]);

        $this->assertNotNull($fuelLog);
        $this->assertDatabaseHas('fuel_logs', ['vehicle_id' => $vehicleId, 'total_cost' => 125.00]);
    }

    public function test_validates_maintenance_data(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->scheduleMaintenance([]);
    }

    public function test_validates_fuel_log_data(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->logFuel([]);
    }

    public function test_get_vehicle_maintenance_history(): void
    {
        $vehicleId = $this->createVehicle();

        $this->service->scheduleMaintenance([
            'school_id' => $this->schoolId,
            'vehicle_id' => $vehicleId,
            'type' => 'preventive',
            'description' => 'Oil change',
            'service_date' => now()->subDays(30)->format('Y-m-d'),
        ]);

        $this->service->scheduleMaintenance([
            'school_id' => $this->schoolId,
            'vehicle_id' => $vehicleId,
            'type' => 'corrective',
            'description' => 'Brake repair',
            'service_date' => now()->format('Y-m-d'),
        ]);

        $history = $this->service->getVehicleMaintenanceHistory($vehicleId);

        $this->assertCount(2, $history);
    }

    public function test_get_total_fuel_cost(): void
    {
        $vehicleId = $this->createVehicle();

        $this->service->logFuel([
            'school_id' => $this->schoolId,
            'vehicle_id' => $vehicleId,
            'liters' => 50,
            'cost_per_liter' => 2.00,
            'total_cost' => 100.00,
            'fuel_type' => 'diesel',
            'fuel_station' => 'Station A',
            'odometer_reading' => 45000,
            'fuel_date' => now()->format('Y-m-d'),
        ]);

        $this->service->logFuel([
            'school_id' => $this->schoolId,
            'vehicle_id' => $vehicleId,
            'liters' => 40,
            'cost_per_liter' => 2.50,
            'total_cost' => 100.00,
            'fuel_type' => 'diesel',
            'fuel_station' => 'Station B',
            'odometer_reading' => 45100,
            'fuel_date' => now()->format('Y-m-d'),
        ]);

        $total = $this->service->getTotalFuelCost($this->schoolId);

        $this->assertEqualsWithDelta(200.00, $total, 0.01);
    }

    protected function createVehicle(): string
    {
        $fleetService = new FleetService(
            new VehicleRules,
            new DriverRules,
            new TripRules
        );

        $vehicle = $fleetService->registerVehicle([
            'school_id' => $this->schoolId,
            'registration_number' => 'GR-MAINT-23',
            'vin' => '1HGCM82633A444444',
            'manufacturer' => 'Toyota',
            'model' => 'HiAce',
            'year' => 2023,
            'capacity' => 14,
            'fuel_type' => 'diesel',
        ]);

        return $vehicle->id;
    }
}
