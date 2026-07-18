<?php

declare(strict_types=1);

namespace App\Domains\Transport\Services;

use App\Domains\Transport\DTOs\FuelLogData;
use App\Domains\Transport\DTOs\VehicleMaintenanceData;
use App\Domains\Transport\Events\FuelLogged;
use App\Domains\Transport\Events\MaintenanceCompleted;
use App\Domains\Transport\Events\MaintenanceScheduled;
use App\Domains\Transport\Rules\FuelLogRules;
use App\Domains\Transport\Rules\VehicleMaintenanceRules;
use App\Models\FuelLog;
use App\Models\VehicleMaintenance;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MaintenanceService
{
    public function __construct(
        protected VehicleMaintenanceRules $maintenanceRules,
        protected FuelLogRules $fuelLogRules,
    ) {
    }

    // Maintenance CRUD
    public function scheduleMaintenance(mixed $data): VehicleMaintenanceData
    {
        $validated = $this->maintenanceRules->validate($data, 'create');

        $maintenance = DB::transaction(function () use ($validated) {
            $maintenance = VehicleMaintenance::create($validated);

            return $maintenance;
        });

        MaintenanceScheduled::dispatch($maintenance);

        return VehicleMaintenanceData::create($maintenance->toArray());
    }

    public function completeMaintenance(string $id, array $data = []): VehicleMaintenanceData
    {
        $maintenance = DB::transaction(function () use ($id, $data) {
            $maintenance = VehicleMaintenance::findOrFail($id);
            $maintenance->update([
                'status' => 'completed',
                'completed_at' => now(),
                'cost' => $data['cost'] ?? $maintenance->cost,
                'notes' => $data['notes'] ?? $maintenance->notes,
            ]);

            return $maintenance;
        });

        MaintenanceCompleted::dispatch($maintenance);

        return VehicleMaintenanceData::create($maintenance->toArray());
    }

    // Fuel Logs
    public function logFuel(mixed $data): FuelLogData
    {
        $validated = $this->fuelLogRules->validate($data, 'create');

        $fuelLog = DB::transaction(function () use ($validated) {
            $fuelLog = FuelLog::create($validated);

            return $fuelLog;
        });

        FuelLogged::dispatch($fuelLog);

        return FuelLogData::create($fuelLog->toArray());
    }

    // Queries
    public function getUpcomingMaintenance(string $schoolId): Collection
    {
        return VehicleMaintenance::where('school_id', $schoolId)
            ->where('status', 'scheduled')
            ->where('next_service_date', '<=', now()->addDays(30))
            ->with('vehicle')
            ->get();
    }

    public function getVehicleMaintenanceHistory(string $vehicleId): Collection
    {
        return VehicleMaintenance::where('vehicle_id', $vehicleId)
            ->orderByDesc('service_date')
            ->get();
    }

    public function getVehicleFuelHistory(string $vehicleId): Collection
    {
        return FuelLog::where('vehicle_id', $vehicleId)
            ->orderByDesc('fuel_date')
            ->get();
    }

    public function getTotalFuelCost(string $schoolId, ?string $startDate = null, ?string $endDate = null): float
    {
        $query = FuelLog::where('school_id', $schoolId);

        if ($startDate) {
            $query->where('fuel_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('fuel_date', '<=', $endDate);
        }

        return (float) $query->sum('total_cost');
    }
}
