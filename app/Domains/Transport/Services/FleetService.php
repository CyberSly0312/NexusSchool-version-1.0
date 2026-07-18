<?php

declare(strict_types=1);

namespace App\Domains\Transport\Services;

use App\Domains\Transport\DTOs\DriverData;
use App\Domains\Transport\DTOs\TripData;
use App\Domains\Transport\DTOs\VehicleData;
use App\Domains\Transport\Events\DriverAssigned;
use App\Domains\Transport\Events\DriverUpdated;
use App\Domains\Transport\Events\TripCancelled;
use App\Domains\Transport\Events\TripCompleted;
use App\Domains\Transport\Events\TripScheduled;
use App\Domains\Transport\Events\TripStarted;
use App\Domains\Transport\Events\VehicleRegistered;
use App\Domains\Transport\Events\VehicleUpdated;
use App\Domains\Transport\Rules\DriverRules;
use App\Domains\Transport\Rules\TripRules;
use App\Domains\Transport\Rules\VehicleRules;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\Vehicle;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FleetService
{
    public function __construct(
        protected VehicleRules $vehicleRules,
        protected DriverRules $driverRules,
        protected TripRules $tripRules,
    ) {
    }

    // Vehicles CRUD
    public function registerVehicle(mixed $data): VehicleData
    {
        $validated = $this->vehicleRules->validate($data, 'create');

        $vehicle = DB::transaction(function () use ($validated) {
            $vehicle = Vehicle::create($validated);

            return $vehicle;
        });

        VehicleRegistered::dispatch($vehicle);

        return VehicleData::create($vehicle->toArray());
    }

    public function updateVehicle(string $id, mixed $data): VehicleData
    {
        $validated = $this->vehicleRules->validate($data, 'update');

        $vehicle = DB::transaction(function () use ($id, $validated) {
            $vehicle = Vehicle::findOrFail($id);
            $vehicle->update($validated);

            return $vehicle;
        });

        VehicleUpdated::dispatch($vehicle);

        return VehicleData::create($vehicle->toArray());
    }

    // Drivers CRUD
    public function assignDriver(mixed $data): DriverData
    {
        $validated = $this->driverRules->validate($data, 'create');

        $driver = DB::transaction(function () use ($validated) {
            $driver = Driver::create($validated);

            return $driver;
        });

        DriverAssigned::dispatch($driver);

        return DriverData::create($driver->toArray());
    }

    public function updateDriver(string $id, mixed $data): DriverData
    {
        $validated = $this->driverRules->validate($data, 'update');

        $driver = DB::transaction(function () use ($id, $validated) {
            $driver = Driver::findOrFail($id);
            $driver->update($validated);

            return $driver;
        });

        DriverUpdated::dispatch($driver);

        return DriverData::create($driver->toArray());
    }

    // Trips
    public function scheduleTrip(mixed $data): TripData
    {
        $validated = $this->tripRules->validate($data, 'create');

        $trip = DB::transaction(function () use ($validated) {
            $trip = Trip::create($validated);

            return $trip;
        });

        TripScheduled::dispatch($trip);

        return TripData::create($trip->toArray());
    }

    public function startTrip(string $id): TripData
    {
        $trip = DB::transaction(function () use ($id) {
            $trip = Trip::findOrFail($id);
            $trip->update([
                'status' => 'started',
                'actual_departure' => now(),
            ]);

            return $trip;
        });

        TripStarted::dispatch($trip);

        return TripData::create($trip->toArray());
    }

    public function completeTrip(string $id): TripData
    {
        $trip = DB::transaction(function () use ($id) {
            $trip = Trip::findOrFail($id);
            $trip->update([
                'status' => 'completed',
                'actual_arrival' => now(),
            ]);

            return $trip;
        });

        TripCompleted::dispatch($trip);

        return TripData::create($trip->toArray());
    }

    public function cancelTrip(string $id): void
    {
        $trip = Trip::findOrFail($id);
        $trip->update(['status' => 'cancelled']);
        TripCancelled::dispatch($trip);
    }

    // Queries
    public function getAvailableVehicles(): Collection
    {
        return Vehicle::where('status', 'available')->get();
    }

    public function getAvailableDrivers(): Collection
    {
        return Driver::where('status', 'available')->get();
    }

    public function getTripsForDate(string $date): Collection
    {
        return Trip::where('academic_date', $date)
            ->with(['vehicle', 'driver', 'route'])
            ->get();
    }
}
