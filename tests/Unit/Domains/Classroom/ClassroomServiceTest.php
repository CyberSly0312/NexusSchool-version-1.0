<?php

namespace Tests\Unit\Domains\Classroom;

use App\Domains\AcademicClass\DTOs\AcademicClassData;
use App\Domains\AcademicClass\Services\AcademicClassService;
use App\Domains\Classroom\DTOs\ClassroomData;
use App\Domains\Classroom\Enums\ClassroomStatus;
use App\Domains\Classroom\Events\ClassroomArchived;
use App\Domains\Classroom\Events\ClassroomAssigned;
use App\Domains\Classroom\Events\ClassroomCreated;
use App\Domains\Classroom\Events\ClassroomReleased;
use App\Domains\Classroom\Events\ClassroomUpdated;
use App\Domains\Classroom\Services\ClassroomService;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\GradeLevel;
use App\Models\Program;
use App\Models\Section;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ClassroomServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ClassroomService $service;

    protected AcademicClassService $academicClassService;

    protected string $schoolId;

    protected AcademicYear $academicYear;

    protected Program $program;

    protected GradeLevel $gradeLevel;

    protected Section $section;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new ClassroomService;
        $this->academicClassService = new AcademicClassService;

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

        DB::table('academic_years')->insert([
            'school_id' => $schoolId,
            'name' => '2025-2026',
            'code' => 'AY-2025-2026',
            'start_date' => '2025-09-01',
            'end_date' => '2026-07-31',
            'status' => 'active',
            'uuid' => Str::ulid(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->academicYear = AcademicYear::where('school_id', $schoolId)
            ->where('name', '2025-2026')
            ->first();

        $this->program = Program::factory()
            ->forSchool($schoolId)
            ->create(['name' => 'Junior High School', 'code' => 'JHS']);

        $this->gradeLevel = GradeLevel::factory()
            ->forProgram($this->program->id)
            ->withOrder(1)
            ->create(['name' => '6ème', 'code' => '6E']);

        $this->section = Section::factory()
            ->forGradeLevel($this->gradeLevel->id)
            ->create(['name' => 'Section A', 'code' => 'A']);
    }

    public function test_can_create_classroom(): void
    {
        $data = ClassroomData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Room 101',
            'code' => 'R101',
            'building' => 'Building A',
            'floor' => '1',
            'capacity' => 40,
            'room_type' => 'standard_classroom',
            'description' => 'Standard classroom',
            'display_order' => 1,
        ]);

        $classroom = $this->service->create($data);

        $this->assertInstanceOf(Classroom::class, $classroom);
        $this->assertEquals('Room 101', $classroom->name);
        $this->assertEquals('R101', $classroom->code);
        $this->assertEquals(40, $classroom->capacity);
        $this->assertEquals(ClassroomStatus::Active, $classroom->status);
    }

    public function test_can_update_classroom(): void
    {
        $classroom = Classroom::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Old Name']);

        $updated = $this->service->update($classroom, ['name' => 'New Name']);

        $this->assertEquals('New Name', $updated->name);
    }

    public function test_can_delete_classroom(): void
    {
        $classroom = Classroom::factory()
            ->forSchool($this->schoolId)
            ->create();

        $deleted = $this->service->delete($classroom);

        $this->assertTrue($deleted);
        $this->assertSoftDeleted('classrooms', ['id' => $classroom->id]);
    }

    public function test_can_archive_classroom(): void
    {
        $classroom = Classroom::factory()
            ->forSchool($this->schoolId)
            ->active()
            ->create();

        $archived = $this->service->archive($classroom);

        $this->assertEquals(ClassroomStatus::Archived, $archived->status);
    }

    public function test_can_activate_classroom(): void
    {
        $classroom = Classroom::factory()
            ->forSchool($this->schoolId)
            ->archived()
            ->create();

        $activated = $this->service->activate($classroom);

        $this->assertEquals(ClassroomStatus::Active, $activated->status);
    }

    public function test_can_assign_classroom_to_academic_class(): void
    {
        $classroom = Classroom::factory()
            ->forSchool($this->schoolId)
            ->withCapacity(40)
            ->active()
            ->create();

        $academicClassData = AcademicClassData::fromArray([
            'academic_year_id' => $this->academicYear->id,
            'program_id' => $this->program->id,
            'grade_level_id' => $this->gradeLevel->id,
            'section_id' => $this->section->id,
            'name' => '6ème A',
            'code' => '6EA',
            'capacity' => 35,
        ]);

        $academicClass = $this->academicClassService->create($academicClassData);

        $result = $this->service->assign($classroom, $academicClass);

        $this->assertNotNull($result);
        $academicClass->refresh();
        $this->assertEquals($classroom->id, $academicClass->classroom_id);
    }

    public function test_can_release_classroom_from_academic_class(): void
    {
        $classroom = Classroom::factory()
            ->forSchool($this->schoolId)
            ->withCapacity(40)
            ->active()
            ->create();

        $academicClassData = AcademicClassData::fromArray([
            'academic_year_id' => $this->academicYear->id,
            'program_id' => $this->program->id,
            'grade_level_id' => $this->gradeLevel->id,
            'section_id' => $this->section->id,
            'name' => '6ème A',
            'code' => '6EA',
            'capacity' => 35,
        ]);

        $academicClass = $this->academicClassService->create($academicClassData);

        $this->service->assign($classroom, $academicClass);
        $result = $this->service->release($classroom);

        $this->assertNotNull($result);
        $academicClass->refresh();
        $this->assertNull($academicClass->classroom_id);
    }

    public function test_duplicate_name_same_school_is_rejected(): void
    {
        Classroom::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Room 101', 'code' => 'R101']);

        $data = ClassroomData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Room 101',
            'code' => 'R102',
            'capacity' => 30,
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_duplicate_code_same_school_is_rejected(): void
    {
        Classroom::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Room 101', 'code' => 'R101']);

        $data = ClassroomData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Room 102',
            'code' => 'R101',
            'capacity' => 30,
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

        Classroom::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Room 101', 'code' => 'R101']);

        $data = ClassroomData::fromArray([
            'school_id' => $otherSchoolId,
            'name' => 'Room 101',
            'code' => 'R101',
            'capacity' => 30,
        ]);

        $classroom = $this->service->create($data);

        $this->assertNotNull($classroom);
        $this->assertEquals('Room 101', $classroom->name);
    }

    public function test_capacity_must_be_greater_than_zero(): void
    {
        $this->expectException(ValidationException::class);

        $data = ClassroomData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Room 101',
            'code' => 'R101',
            'capacity' => 0,
        ]);

        $this->service->create($data);
    }

    public function test_can_paginate_classrooms(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            Classroom::factory()
                ->forSchool($this->schoolId)
                ->withOrder($i)
                ->create();
        }

        $result = $this->service->paginate([], 10);

        $this->assertEquals(5, $result->total());
    }

    public function test_can_filter_by_status(): void
    {
        for ($i = 1; $i <= 3; $i++) {
            Classroom::factory()
                ->forSchool($this->schoolId)
                ->active()
                ->withOrder($i)
                ->create();
        }
        Classroom::factory()
            ->forSchool($this->schoolId)
            ->archived()
            ->withOrder(4)
            ->create();

        $result = $this->service->paginate(['status' => ClassroomStatus::Active->value], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_can_filter_by_room_type(): void
    {
        Classroom::factory()
            ->forSchool($this->schoolId)
            ->standard()
            ->withOrder(1)
            ->create(['name' => 'Room 1']);
        Classroom::factory()
            ->forSchool($this->schoolId)
            ->laboratory()
            ->withOrder(2)
            ->create(['name' => 'Lab 1']);

        $result = $this->service->paginate(['room_type' => 'standard_classroom'], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_can_search_by_name(): void
    {
        Classroom::factory()
            ->forSchool($this->schoolId)
            ->withOrder(1)
            ->create(['name' => 'Room 101', 'code' => 'R101']);
        Classroom::factory()
            ->forSchool($this->schoolId)
            ->withOrder(2)
            ->create(['name' => 'Lab 1', 'code' => 'LAB1']);

        $result = $this->service->paginate(['search' => 'Room'], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_can_search_by_code(): void
    {
        Classroom::factory()
            ->forSchool($this->schoolId)
            ->withOrder(1)
            ->create(['name' => 'Room 101', 'code' => 'R101']);
        Classroom::factory()
            ->forSchool($this->schoolId)
            ->withOrder(2)
            ->create(['name' => 'Lab 1', 'code' => 'LAB1']);

        $result = $this->service->paginate(['search' => 'R101'], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_events_dispatched_on_create(): void
    {
        Event::fake([ClassroomCreated::class]);

        $data = ClassroomData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Room 101',
            'code' => 'R101',
            'capacity' => 30,
        ]);

        $this->service->create($data);

        Event::assertDispatched(ClassroomCreated::class);
    }

    public function test_events_dispatched_on_update(): void
    {
        $classroom = Classroom::factory()
            ->forSchool($this->schoolId)
            ->create();

        Event::fake([ClassroomUpdated::class]);

        $this->service->update($classroom, ['name' => 'Updated']);

        Event::assertDispatched(ClassroomUpdated::class);
    }

    public function test_events_dispatched_on_archive(): void
    {
        $classroom = Classroom::factory()
            ->forSchool($this->schoolId)
            ->active()
            ->create();

        Event::fake([ClassroomArchived::class]);

        $this->service->archive($classroom);

        Event::assertDispatched(ClassroomArchived::class);
    }

    public function test_events_dispatched_on_assign(): void
    {
        $classroom = Classroom::factory()
            ->forSchool($this->schoolId)
            ->withCapacity(40)
            ->active()
            ->create();

        $academicClassData = AcademicClassData::fromArray([
            'academic_year_id' => $this->academicYear->id,
            'program_id' => $this->program->id,
            'grade_level_id' => $this->gradeLevel->id,
            'section_id' => $this->section->id,
            'name' => '6ème A',
            'code' => '6EA',
            'capacity' => 35,
        ]);

        $academicClass = $this->academicClassService->create($academicClassData);

        Event::fake([ClassroomAssigned::class]);

        $this->service->assign($classroom, $academicClass);

        Event::assertDispatched(ClassroomAssigned::class);
    }

    public function test_events_dispatched_on_release(): void
    {
        $classroom = Classroom::factory()
            ->forSchool($this->schoolId)
            ->withCapacity(40)
            ->active()
            ->create();

        $academicClassData = AcademicClassData::fromArray([
            'academic_year_id' => $this->academicYear->id,
            'program_id' => $this->program->id,
            'grade_level_id' => $this->gradeLevel->id,
            'section_id' => $this->section->id,
            'name' => '6ème A',
            'code' => '6EA',
            'capacity' => 35,
        ]);

        $academicClass = $this->academicClassService->create($academicClassData);

        $this->service->assign($classroom, $academicClass);

        Event::fake([ClassroomReleased::class]);

        $this->service->release($classroom);

        Event::assertDispatched(ClassroomReleased::class);
    }

    public function test_can_find_by_id(): void
    {
        $classroom = Classroom::factory()
            ->forSchool($this->schoolId)
            ->create();

        $found = $this->service->findById($classroom->id);

        $this->assertEquals($classroom->id, $found->id);
    }

    public function test_find_by_id_throws_when_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->findById(99999);
    }

    public function test_assign_validates_capacity(): void
    {
        $classroom = Classroom::factory()
            ->forSchool($this->schoolId)
            ->withCapacity(10)
            ->active()
            ->create();

        $academicClassData = AcademicClassData::fromArray([
            'academic_year_id' => $this->academicYear->id,
            'program_id' => $this->program->id,
            'grade_level_id' => $this->gradeLevel->id,
            'section_id' => $this->section->id,
            'name' => '6ème A',
            'code' => '6EA',
            'capacity' => 40,
        ]);

        $academicClass = $this->academicClassService->create($academicClassData);

        $this->expectException(ValidationException::class);

        $this->service->assign($classroom, $academicClass);
    }

    public function test_assign_validates_classroom_is_active(): void
    {
        $classroom = Classroom::factory()
            ->forSchool($this->schoolId)
            ->withCapacity(40)
            ->archived()
            ->create();

        $academicClassData = AcademicClassData::fromArray([
            'academic_year_id' => $this->academicYear->id,
            'program_id' => $this->program->id,
            'grade_level_id' => $this->gradeLevel->id,
            'section_id' => $this->section->id,
            'name' => '6ème A',
            'code' => '6EA',
            'capacity' => 35,
        ]);

        $academicClass = $this->academicClassService->create($academicClassData);

        $this->expectException(ValidationException::class);

        $this->service->assign($classroom, $academicClass);
    }

    public function test_update_validates_uniqueness_per_school(): void
    {
        Classroom::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Room 101', 'code' => 'R101']);

        $class2 = Classroom::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Room 102', 'code' => 'R102']);

        $this->expectException(ValidationException::class);

        $this->service->update($class2, ['name' => 'Room 101']);
    }

    public function test_can_update_same_classroom_name(): void
    {
        $classroom = Classroom::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Room 101', 'code' => 'R101']);

        $updated = $this->service->update($classroom, ['description' => 'Updated']);

        $this->assertEquals('Room 101', $updated->name);
        $this->assertEquals('Updated', $updated->description);
    }

    public function test_uuid_is_auto_generated(): void
    {
        $data = ClassroomData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Room 101',
            'code' => 'R101',
            'capacity' => 30,
        ]);

        $classroom = $this->service->create($data);

        $this->assertNotEmpty($classroom->uuid);
    }
}
