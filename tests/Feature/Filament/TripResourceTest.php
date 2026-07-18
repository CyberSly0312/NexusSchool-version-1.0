<?php

namespace Tests\Feature\Filament;

use App\Models\Driver;
use App\Models\Route;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class TripResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['status' => 'active']);
        $this->actingAs($this->admin);

        Filament::setServingStatus();
        Filament::setCurrentPanel('admin');
    }

    public function test_can_render_trips_list_page(): void
    {
        $this->get(route('filament.admin.resources.trips.index'))
            ->assertSuccessful();
    }

    public function test_can_render_create_trip_page(): void
    {
        $this->get(route('filament.admin.resources.trips.create'))
            ->assertSuccessful();
    }

    public function test_can_render_edit_trip_page(): void
    {
        $schoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $schoolId,
            'uuid' => Str::ulid(),
            'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(),
            'database' => 'school_test',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $vehicle = Vehicle::factory()->create(['school_id' => $schoolId]);
        $driver = Driver::factory()->create(['school_id' => $schoolId]);
        $route = Route::factory()->create(['school_id' => $schoolId]);
        $trip = Trip::factory()->create([
            'school_id' => $schoolId,
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'route_id' => $route->id,
        ]);

        $this->get(route('filament.admin.resources.trips.edit', ['record' => $trip]))
            ->assertSuccessful();
    }
}
