<?php

namespace Tests\Unit\Domains\Program;

use App\Domains\Program\DTOs\ProgramData;
use App\Domains\Program\Enums\EducationalLevel;
use App\Domains\Program\Enums\ProgramStatus;
use App\Domains\Program\Events\ProgramArchived;
use App\Domains\Program\Events\ProgramCreated;
use App\Domains\Program\Events\ProgramUpdated;
use App\Domains\Program\Services\ProgramService;
use App\Models\Department;
use App\Models\Program;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ProgramServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProgramService $service;

    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new ProgramService;

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

    public function test_can_create_program(): void
    {
        $data = ProgramData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Junior High School',
            'code' => 'JHS',
            'educational_level' => EducationalLevel::JuniorSecondary->value,
            'duration' => '3 years',
            'description' => 'Junior secondary education',
            'order' => 1,
        ]);

        $program = $this->service->create($data);

        $this->assertInstanceOf(Program::class, $program);
        $this->assertEquals('Junior High School', $program->name);
        $this->assertEquals('JHS', $program->code);
        $this->assertEquals(EducationalLevel::JuniorSecondary, $program->educational_level);
        $this->assertEquals('3 years', $program->duration);
        $this->assertEquals(ProgramStatus::Active, $program->status);
        $this->assertEquals(1, $program->order);
    }

    public function test_can_create_program_with_department(): void
    {
        $department = Department::factory()
            ->forSchool($this->schoolId)
            ->create();

        $data = ProgramData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Science Program',
            'code' => 'SCI',
            'educational_level' => EducationalLevel::SeniorSecondary->value,
            'duration' => '3 years',
            'department_id' => $department->id,
        ]);

        $program = $this->service->create($data);

        $this->assertNotNull($program->department_id);
        $this->assertEquals($department->id, $program->department_id);
    }

    public function test_can_update_program(): void
    {
        $program = Program::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Old Name']);

        $updated = $this->service->update($program, ['name' => 'New Name']);

        $this->assertEquals('New Name', $updated->name);
    }

    public function test_can_delete_program(): void
    {
        $program = Program::factory()
            ->forSchool($this->schoolId)
            ->create();

        $deleted = $this->service->delete($program);

        $this->assertTrue($deleted);
        $this->assertSoftDeleted('programs', ['id' => $program->id]);
    }

    public function test_can_archive_program(): void
    {
        $program = Program::factory()
            ->forSchool($this->schoolId)
            ->active()
            ->create();

        $archived = $this->service->archive($program);

        $this->assertEquals(ProgramStatus::Archived, $archived->status);
    }

    public function test_can_restore_program(): void
    {
        $program = Program::factory()
            ->forSchool($this->schoolId)
            ->archived()
            ->create();

        $restored = $this->service->restore($program);

        $this->assertEquals(ProgramStatus::Active, $restored->status);
    }

    public function test_duplicate_name_same_school_is_rejected(): void
    {
        Program::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Junior High School', 'code' => 'JHS']);

        $data = ProgramData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Junior High School',
            'code' => 'JHS2',
            'educational_level' => EducationalLevel::JuniorSecondary->value,
            'duration' => '3 years',
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_duplicate_code_same_school_is_rejected(): void
    {
        Program::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Junior High School', 'code' => 'JHS']);

        $data = ProgramData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Senior High School',
            'code' => 'JHS',
            'educational_level' => EducationalLevel::SeniorSecondary->value,
            'duration' => '3 years',
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

        Program::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Junior High School', 'code' => 'JHS']);

        $data = ProgramData::fromArray([
            'school_id' => $otherSchoolId,
            'name' => 'Junior High School',
            'code' => 'JHS',
            'educational_level' => EducationalLevel::JuniorSecondary->value,
            'duration' => '3 years',
        ]);

        $program = $this->service->create($data);

        $this->assertNotNull($program);
        $this->assertEquals('Junior High School', $program->name);
    }

    public function test_can_paginate_programs(): void
    {
        Program::factory()
            ->forSchool($this->schoolId)
            ->count(5)
            ->create();

        $result = $this->service->paginate([], 10);

        $this->assertEquals(5, $result->total());
    }

    public function test_can_filter_by_status(): void
    {
        Program::factory()
            ->forSchool($this->schoolId)
            ->active()
            ->count(3)
            ->create();
        Program::factory()
            ->forSchool($this->schoolId)
            ->archived()
            ->create();

        $result = $this->service->paginate(['status' => ProgramStatus::Active->value], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_can_search_by_name(): void
    {
        Program::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Junior High School']);
        Program::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Senior High School']);

        $result = $this->service->paginate(['search' => 'Junior'], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_can_search_by_code(): void
    {
        Program::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Junior High School', 'code' => 'JHS']);
        Program::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Senior High School', 'code' => 'SHS']);

        $result = $this->service->paginate(['search' => 'JHS'], 10);

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

        Program::factory()
            ->forSchool($this->schoolId)
            ->count(3)
            ->create();
        Program::factory()
            ->forSchool($otherSchoolId)
            ->count(2)
            ->create();

        $result = $this->service->paginate(['school_id' => $this->schoolId], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_can_filter_by_department(): void
    {
        $department = Department::factory()
            ->forSchool($this->schoolId)
            ->create();

        Program::factory()
            ->forSchool($this->schoolId)
            ->forDepartment($department->id)
            ->count(2)
            ->create();
        Program::factory()
            ->forSchool($this->schoolId)
            ->count(3)
            ->create();

        $result = $this->service->paginate(['department_id' => $department->id], 10);

        $this->assertEquals(2, $result->total());
    }

    public function test_events_dispatched_on_create(): void
    {
        Event::fake([ProgramCreated::class]);

        $data = ProgramData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Junior High School',
            'code' => 'JHS',
            'educational_level' => EducationalLevel::JuniorSecondary->value,
            'duration' => '3 years',
        ]);

        $this->service->create($data);

        Event::assertDispatched(ProgramCreated::class);
    }

    public function test_events_dispatched_on_update(): void
    {
        $program = Program::factory()
            ->forSchool($this->schoolId)
            ->create();

        Event::fake([ProgramUpdated::class]);

        $this->service->update($program, ['name' => 'Updated Name']);

        Event::assertDispatched(ProgramUpdated::class);
    }

    public function test_events_dispatched_on_archive(): void
    {
        $program = Program::factory()
            ->forSchool($this->schoolId)
            ->active()
            ->create();

        Event::fake([ProgramArchived::class]);

        $this->service->archive($program);

        Event::assertDispatched(ProgramArchived::class);
    }

    public function test_can_find_by_id(): void
    {
        $program = Program::factory()
            ->forSchool($this->schoolId)
            ->create();

        $found = $this->service->findById($program->id);

        $this->assertEquals($program->id, $found->id);
    }

    public function test_find_by_id_throws_when_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->findById(99999);
    }

    public function test_update_validates_uniqueness_per_school(): void
    {
        Program::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Junior High School', 'code' => 'JHS']);

        $program2 = Program::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Senior High School', 'code' => 'SHS']);

        $this->expectException(ValidationException::class);

        $this->service->update($program2, ['name' => 'Junior High School']);
    }

    public function test_can_update_same_program_name(): void
    {
        $program = Program::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Junior High School', 'code' => 'JHS']);

        $updated = $this->service->update($program, ['description' => 'Updated description']);

        $this->assertEquals('Junior High School', $updated->name);
        $this->assertEquals('Updated description', $updated->description);
    }

    public function test_uuid_is_auto_generated(): void
    {
        $data = ProgramData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Junior High School',
            'code' => 'JHS',
            'educational_level' => EducationalLevel::JuniorSecondary->value,
            'duration' => '3 years',
        ]);

        $program = $this->service->create($data);

        $this->assertNotEmpty($program->uuid);
    }

    public function test_program_can_be_created_without_department(): void
    {
        $data = ProgramData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Junior High School',
            'code' => 'JHS',
            'educational_level' => EducationalLevel::JuniorSecondary->value,
            'duration' => '3 years',
        ]);

        $program = $this->service->create($data);

        $this->assertNull($program->department_id);
    }

    public function test_educational_level_is_cast_to_enum(): void
    {
        $data = ProgramData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Science Program',
            'code' => 'SCI',
            'educational_level' => EducationalLevel::SeniorSecondary->value,
            'duration' => '3 years',
        ]);

        $program = $this->service->create($data);

        $this->assertInstanceOf(EducationalLevel::class, $program->educational_level);
    }
}
