<?php

namespace Tests\Unit\Domains\Department;

use App\Domains\Department\DTOs\DepartmentData;
use App\Domains\Department\Enums\DepartmentStatus;
use App\Domains\Department\Events\DepartmentArchived;
use App\Domains\Department\Events\DepartmentCreated;
use App\Domains\Department\Events\DepartmentUpdated;
use App\Domains\Department\Services\DepartmentService;
use App\Models\Department;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DepartmentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DepartmentService $service;

    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new DepartmentService;

        $schoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $schoolId,
            'uuid' => Str::ulid(),
            'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(),
            'database' => 'school_test_school',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->schoolId = $schoolId;
    }

    public function test_can_create_department(): void
    {
        $data = DepartmentData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Science Department',
            'code' => 'SCI',
            'description' => 'Science and technology',
            'color' => '#22c55e',
            'order' => 1,
        ]);

        $department = $this->service->create($data);

        $this->assertInstanceOf(Department::class, $department);
        $this->assertEquals('Science Department', $department->name);
        $this->assertEquals('SCI', $department->code);
        $this->assertEquals('#22c55e', $department->color);
        $this->assertEquals(DepartmentStatus::Active, $department->status);
        $this->assertEquals(1, $department->order);
    }

    public function test_can_update_department(): void
    {
        $department = Department::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Old Name']);

        $updated = $this->service->update($department, ['name' => 'New Name']);

        $this->assertEquals('New Name', $updated->name);
    }

    public function test_can_delete_department(): void
    {
        $department = Department::factory()
            ->forSchool($this->schoolId)
            ->create();

        $deleted = $this->service->delete($department);

        $this->assertTrue($deleted);
        $this->assertSoftDeleted('departments', ['id' => $department->id]);
    }

    public function test_can_archive_department(): void
    {
        $department = Department::factory()
            ->forSchool($this->schoolId)
            ->active()
            ->create();

        $archived = $this->service->archive($department);

        $this->assertEquals(DepartmentStatus::Archived, $archived->status);
    }

    public function test_can_restore_archived_department(): void
    {
        $department = Department::factory()
            ->forSchool($this->schoolId)
            ->archived()
            ->create();

        $restored = $this->service->restore($department);

        $this->assertEquals(DepartmentStatus::Active, $restored->status);
    }

    public function test_duplicate_name_same_school_is_rejected(): void
    {
        Department::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Science Department', 'code' => 'SCI']);

        $data = DepartmentData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Science Department',
            'code' => 'SCI2',
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_duplicate_code_same_school_is_rejected(): void
    {
        Department::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Science Department', 'code' => 'SCI']);

        $data = DepartmentData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Arts Department',
            'code' => 'SCI',
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_same_name_allowed_in_different_schools(): void
    {
        $otherSchoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $otherSchoolId,
            'uuid' => Str::ulid(),
            'name' => 'Other School',
            'slug' => 'other-school-'.uniqid(),
            'database' => 'school_other',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Department::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Science Department', 'code' => 'SCI']);

        $data = DepartmentData::fromArray([
            'school_id' => $otherSchoolId,
            'name' => 'Science Department',
            'code' => 'SCI',
        ]);

        $department = $this->service->create($data);

        $this->assertNotNull($department);
        $this->assertEquals('Science Department', $department->name);
    }

    public function test_can_paginate_departments(): void
    {
        Department::factory()
            ->forSchool($this->schoolId)
            ->count(5)
            ->create();

        $result = $this->service->paginate([], 10);

        $this->assertEquals(5, $result->total());
    }

    public function test_can_filter_by_status(): void
    {
        Department::factory()
            ->forSchool($this->schoolId)
            ->active()
            ->count(3)
            ->create();
        Department::factory()
            ->forSchool($this->schoolId)
            ->archived()
            ->create();

        $result = $this->service->paginate(['status' => DepartmentStatus::Active->value], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_can_search_by_name(): void
    {
        Department::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Science Department']);
        Department::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Arts Department']);

        $result = $this->service->paginate(['search' => 'Science'], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_can_search_by_code(): void
    {
        Department::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Science Department', 'code' => 'SCI']);
        Department::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Arts Department', 'code' => 'ART']);

        $result = $this->service->paginate(['search' => 'SCI'], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_can_filter_by_school(): void
    {
        $otherSchoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $otherSchoolId,
            'uuid' => Str::ulid(),
            'name' => 'Other School',
            'slug' => 'other-school-'.uniqid(),
            'database' => 'school_other',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Department::factory()
            ->forSchool($this->schoolId)
            ->count(3)
            ->create();
        Department::factory()
            ->forSchool($otherSchoolId)
            ->count(2)
            ->create();

        $result = $this->service->paginate(['school_id' => $this->schoolId], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_events_dispatched_on_create(): void
    {
        Event::fake([DepartmentCreated::class]);

        $data = DepartmentData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Science Department',
            'code' => 'SCI',
        ]);

        $this->service->create($data);

        Event::assertDispatched(DepartmentCreated::class);
    }

    public function test_events_dispatched_on_update(): void
    {
        $department = Department::factory()
            ->forSchool($this->schoolId)
            ->create();

        Event::fake([DepartmentUpdated::class]);

        $this->service->update($department, ['name' => 'Updated Name']);

        Event::assertDispatched(DepartmentUpdated::class);
    }

    public function test_events_dispatched_on_archive(): void
    {
        $department = Department::factory()
            ->forSchool($this->schoolId)
            ->active()
            ->create();

        Event::fake([DepartmentArchived::class]);

        $this->service->archive($department);

        Event::assertDispatched(DepartmentArchived::class);
    }

    public function test_can_find_by_id(): void
    {
        $department = Department::factory()
            ->forSchool($this->schoolId)
            ->create();

        $found = $this->service->findById($department->id);

        $this->assertEquals($department->id, $found->id);
    }

    public function test_find_by_id_throws_when_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->findById(99999);
    }

    public function test_update_validates_uniqueness_per_school(): void
    {
        $dept1 = Department::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Science Department', 'code' => 'SCI']);

        $dept2 = Department::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Arts Department', 'code' => 'ART']);

        $this->expectException(ValidationException::class);

        $this->service->update($dept2, ['name' => 'Science Department']);
    }

    public function test_can_update_same_department_name(): void
    {
        $department = Department::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Science Department', 'code' => 'SCI']);

        $updated = $this->service->update($department, ['description' => 'Updated description']);

        $this->assertEquals('Science Department', $updated->name);
        $this->assertEquals('Updated description', $updated->description);
    }

    public function test_uuid_is_auto_generated(): void
    {
        $data = DepartmentData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Science Department',
            'code' => 'SCI',
        ]);

        $department = $this->service->create($data);

        $this->assertNotEmpty($department->uuid);
    }
}
