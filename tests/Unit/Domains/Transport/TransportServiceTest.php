<?php

namespace Tests\Unit\Domains\Transport;

use App\Domains\Transport\Rules\RouteRules;
use App\Domains\Transport\Rules\RouteStopRules;
use App\Domains\Transport\Rules\StudentTransportAssignmentRules;
use App\Domains\Transport\Services\TransportService;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class TransportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TransportService $service;

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

        $this->service = new TransportService(
            new RouteRules,
            new RouteStopRules,
            new StudentTransportAssignmentRules
        );
    }

    public function test_can_create_route(): void
    {
        $data = [
            'school_id' => $this->schoolId,
            'name' => 'Route A - North',
            'description' => 'Morning route to the north',
            'distance_km' => 12.50,
            'estimated_duration_minutes' => 45,
            'fare' => 200.00,
        ];

        $route = $this->service->createRoute($data);

        $this->assertNotNull($route);
        $this->assertDatabaseHas('routes', ['name' => 'Route A - North', 'school_id' => $this->schoolId]);
    }

    public function test_can_update_route(): void
    {
        $route = $this->service->createRoute([
            'school_id' => $this->schoolId,
            'name' => 'Route A',
            'description' => 'Morning route',
        ]);

        $updated = $this->service->updateRoute($route->id, [
            'name' => 'Route A Updated',
            'description' => 'Updated morning route',
        ]);

        $this->assertDatabaseHas('routes', ['name' => 'Route A Updated', 'id' => $route->id]);
    }

    public function test_can_archive_route(): void
    {
        $route = $this->service->createRoute([
            'school_id' => $this->schoolId,
            'name' => 'Route A',
            'description' => 'Morning route',
        ]);

        $this->service->archiveRoute($route->id);

        $this->assertDatabaseHas('routes', [
            'id' => $route->id,
            'status' => 'archived',
        ]);
    }

    public function test_can_add_stop(): void
    {
        $route = $this->service->createRoute([
            'school_id' => $this->schoolId,
            'name' => 'Route A',
            'description' => 'Morning route',
        ]);

        $stop = $this->service->addStop([
            'school_id' => $this->schoolId,
            'route_id' => $route->id,
            'name' => 'Market Square',
            'stop_order' => 1,
            'latitude' => 6.5244,
            'longitude' => 3.3792,
        ]);

        $this->assertNotNull($stop);
        $this->assertDatabaseHas('route_stops', ['name' => 'Market Square']);
    }

    public function test_can_remove_stop(): void
    {
        $route = $this->service->createRoute([
            'school_id' => $this->schoolId,
            'name' => 'Route A',
            'description' => 'Morning route',
        ]);

        $stop = $this->service->addStop([
            'school_id' => $this->schoolId,
            'route_id' => $route->id,
            'name' => 'Stop 1',
            'stop_order' => 1,
        ]);

        $this->service->removeStop($stop->id);

        $this->assertSoftDeleted('route_stops', ['id' => $stop->id]);
    }

    public function test_can_assign_student(): void
    {
        $student = Student::factory()->create(['school_id' => $this->schoolId]);
        $academicYearId = \DB::table('academic_years')->insertGetId([
            'school_id' => $this->schoolId,
            'uuid' => Str::ulid(),
            'name' => '2026',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addYear()->format('Y-m-d'),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $route = $this->service->createRoute([
            'school_id' => $this->schoolId,
            'name' => 'Route A',
            'description' => 'Morning route',
        ]);

        $assignment = $this->service->assignStudent([
            'school_id' => $this->schoolId,
            'student_id' => (string) $student->id,
            'route_id' => $route->id,
            'academic_year_id' => (string) $academicYearId,
        ]);

        $this->assertNotNull($assignment);
        $this->assertDatabaseHas('student_transport_assignments', [
            'student_id' => $student->id,
            'route_id' => $route->id,
        ]);
    }

    public function test_can_unassign_student(): void
    {
        $student = Student::factory()->create(['school_id' => $this->schoolId]);
        $academicYearId = \DB::table('academic_years')->insertGetId([
            'school_id' => $this->schoolId,
            'uuid' => Str::ulid(),
            'name' => '2026',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addYear()->format('Y-m-d'),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $route = $this->service->createRoute([
            'school_id' => $this->schoolId,
            'name' => 'Route A',
            'description' => 'Morning route',
        ]);

        $assignment = $this->service->assignStudent([
            'school_id' => $this->schoolId,
            'student_id' => (string) $student->id,
            'route_id' => $route->id,
            'academic_year_id' => (string) $academicYearId,
        ]);

        $this->service->unassignStudent($assignment->id);

        $this->assertSoftDeleted('student_transport_assignments', [
            'id' => $assignment->id,
        ]);
    }

    public function test_validates_route_data(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->createRoute([]);
    }

    public function test_get_route_with_stops(): void
    {
        $route = $this->service->createRoute([
            'school_id' => $this->schoolId,
            'name' => 'Route A',
            'description' => 'Morning route',
        ]);

        $this->service->addStop([
            'school_id' => $this->schoolId,
            'route_id' => $route->id,
            'name' => 'Stop 1',
            'stop_order' => 1,
        ]);

        $this->service->addStop([
            'school_id' => $this->schoolId,
            'route_id' => $route->id,
            'name' => 'Stop 2',
            'stop_order' => 2,
        ]);

        $routeWithStops = $this->service->getRouteWithStops($route->id);

        $this->assertCount(2, $routeWithStops->stops);
    }
}
