<?php

declare(strict_types=1);

namespace App\Domains\Transport\Services;

use App\Domains\Transport\DTOs\RouteData;
use App\Domains\Transport\DTOs\RouteStopData;
use App\Domains\Transport\DTOs\StudentTransportAssignmentData;
use App\Domains\Transport\Events\RouteArchived;
use App\Domains\Transport\Events\RouteCreated;
use App\Domains\Transport\Events\RouteUpdated;
use App\Domains\Transport\Events\StopAdded;
use App\Domains\Transport\Events\StopRemoved;
use App\Domains\Transport\Events\StudentAssigned;
use App\Domains\Transport\Events\StudentUnassigned;
use App\Domains\Transport\Rules\RouteRules;
use App\Domains\Transport\Rules\RouteStopRules;
use App\Domains\Transport\Rules\StudentTransportAssignmentRules;
use App\Models\Route;
use App\Models\RouteStop;
use App\Models\StudentTransportAssignment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TransportService
{
    public function __construct(
        protected RouteRules $routeRules,
        protected RouteStopRules $stopRules,
        protected StudentTransportAssignmentRules $assignmentRules,
    ) {
    }

    // Routes CRUD
    public function createRoute(mixed $data): RouteData
    {
        $validated = $this->routeRules->validate($data, 'create');

        $route = DB::transaction(function () use ($validated) {
            $route = Route::create($validated);

            return $route;
        });

        RouteCreated::dispatch($route);

        return RouteData::create($route->toArray());
    }

    public function updateRoute(string $id, mixed $data): RouteData
    {
        $validated = $this->routeRules->validate($data, 'update');

        $route = DB::transaction(function () use ($id, $validated) {
            $route = Route::findOrFail($id);
            $route->update($validated);

            return $route;
        });

        RouteUpdated::dispatch($route);

        return RouteData::create($route->toArray());
    }

    public function archiveRoute(string $id): void
    {
        $route = Route::findOrFail($id);
        $route->update(['status' => 'archived']);
        RouteArchived::dispatch($route);
    }

    // Route Stops
    public function addStop(mixed $data): RouteStopData
    {
        $validated = $this->stopRules->validate($data, 'create');

        $stop = DB::transaction(function () use ($validated) {
            $stop = RouteStop::create($validated);

            return $stop;
        });

        StopAdded::dispatch($stop);

        return RouteStopData::create($stop->toArray());
    }

    public function removeStop(string $id): void
    {
        $stop = RouteStop::findOrFail($id);
        $stop->delete();
        StopRemoved::dispatch($stop);
    }

    // Student Transport Assignments
    public function assignStudent(mixed $data): StudentTransportAssignmentData
    {
        $validated = $this->assignmentRules->validate($data, 'create');

        $assignment = DB::transaction(function () use ($validated) {
            $assignment = StudentTransportAssignment::create($validated);

            return $assignment;
        });

        StudentAssigned::dispatch($assignment);

        return StudentTransportAssignmentData::create($assignment->toArray());
    }

    public function unassignStudent(string $id): void
    {
        $assignment = StudentTransportAssignment::findOrFail($id);
        $assignment->delete();
        StudentUnassigned::dispatch($assignment);
    }

    // Queries
    public function getRouteWithStops(string $routeId): Route
    {
        return Route::with('stops')->findOrFail($routeId);
    }

    public function getRouteAssignments(string $routeId): Collection
    {
        return StudentTransportAssignment::where('route_id', $routeId)
            ->where('status', 'active')
            ->with('student')
            ->get();
    }
}
