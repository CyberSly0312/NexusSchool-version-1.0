<?php

namespace Tests\Unit\Domains\AcademicClass;

use App\Domains\AcademicClass\DTOs\AcademicClassData;
use App\Domains\AcademicClass\Enums\AcademicClassStatus;
use App\Domains\AcademicClass\Events\AcademicClassActivated;
use App\Domains\AcademicClass\Events\AcademicClassArchived;
use App\Domains\AcademicClass\Events\AcademicClassCreated;
use App\Domains\AcademicClass\Events\AcademicClassUpdated;
use App\Domains\AcademicClass\Services\AcademicClassService;
use App\Models\AcademicClass;
use App\Models\AcademicYear;
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

class AcademicClassServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AcademicClassService $service;

    protected string $schoolId;

    protected AcademicYear $academicYear;

    protected Program $program;

    protected GradeLevel $gradeLevel;

    protected Section $section;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new AcademicClassService;

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

    public function test_can_create_academic_class(): void
    {
        $data = AcademicClassData::fromArray([
            'academic_year_id' => $this->academicYear->id,
            'program_id' => $this->program->id,
            'grade_level_id' => $this->gradeLevel->id,
            'section_id' => $this->section->id,
            'name' => '6ème A',
            'code' => '6EA',
            'description' => 'First class',
            'capacity' => 40,
            'display_order' => 1,
            'is_active' => true,
        ]);

        $academicClass = $this->service->create($data);

        $this->assertInstanceOf(AcademicClass::class, $academicClass);
        $this->assertEquals('6ème A', $academicClass->name);
        $this->assertEquals('6EA', $academicClass->code);
        $this->assertEquals(40, $academicClass->capacity);
        $this->assertEquals(1, $academicClass->display_order);
        $this->assertEquals(AcademicClassStatus::Active, $academicClass->status);
        $this->assertTrue($academicClass->is_active);
    }

    public function test_can_update_academic_class(): void
    {
        $academicClass = AcademicClass::factory()
            ->forAcademicYear($this->academicYear->id)
            ->forProgram($this->program->id)
            ->forGradeLevel($this->gradeLevel->id)
            ->forSection($this->section->id)
            ->create(['name' => 'Old Name']);

        $updated = $this->service->update($academicClass, ['name' => 'New Name']);

        $this->assertEquals('New Name', $updated->name);
    }

    public function test_can_delete_academic_class(): void
    {
        $academicClass = AcademicClass::factory()
            ->forAcademicYear($this->academicYear->id)
            ->forProgram($this->program->id)
            ->forGradeLevel($this->gradeLevel->id)
            ->forSection($this->section->id)
            ->create();

        $deleted = $this->service->delete($academicClass);

        $this->assertTrue($deleted);
        $this->assertSoftDeleted('academic_classes', ['id' => $academicClass->id]);
    }

    public function test_can_archive_academic_class(): void
    {
        $academicClass = AcademicClass::factory()
            ->forAcademicYear($this->academicYear->id)
            ->forProgram($this->program->id)
            ->forGradeLevel($this->gradeLevel->id)
            ->forSection($this->section->id)
            ->active()
            ->create();

        $archived = $this->service->archive($academicClass);

        $this->assertEquals(AcademicClassStatus::Archived, $archived->status);
    }

    public function test_can_activate_academic_class(): void
    {
        $academicClass = AcademicClass::factory()
            ->forAcademicYear($this->academicYear->id)
            ->forProgram($this->program->id)
            ->forGradeLevel($this->gradeLevel->id)
            ->forSection($this->section->id)
            ->archived()
            ->create();

        $activated = $this->service->activate($academicClass);

        $this->assertEquals(AcademicClassStatus::Active, $activated->status);
    }

    public function test_can_toggle_active(): void
    {
        $academicClass = AcademicClass::factory()
            ->forAcademicYear($this->academicYear->id)
            ->forProgram($this->program->id)
            ->forGradeLevel($this->gradeLevel->id)
            ->forSection($this->section->id)
            ->enabled()
            ->create();

        $toggled = $this->service->toggleActive($academicClass);

        $this->assertFalse($toggled->is_active);
    }

    public function test_duplicate_name_same_year_is_rejected(): void
    {
        AcademicClass::factory()
            ->forAcademicYear($this->academicYear->id)
            ->forProgram($this->program->id)
            ->forGradeLevel($this->gradeLevel->id)
            ->forSection($this->section->id)
            ->create(['name' => '6ème A', 'code' => '6EA']);

        $data = AcademicClassData::fromArray([
            'academic_year_id' => $this->academicYear->id,
            'program_id' => $this->program->id,
            'grade_level_id' => $this->gradeLevel->id,
            'section_id' => $this->section->id,
            'name' => '6ème A',
            'code' => '6EB',
            'capacity' => 30,
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_duplicate_code_same_school_is_rejected(): void
    {
        AcademicClass::factory()
            ->forAcademicYear($this->academicYear->id)
            ->forProgram($this->program->id)
            ->forGradeLevel($this->gradeLevel->id)
            ->forSection($this->section->id)
            ->create(['name' => '6ème A', 'code' => '6EA']);

        $data = AcademicClassData::fromArray([
            'academic_year_id' => $this->academicYear->id,
            'program_id' => $this->program->id,
            'grade_level_id' => $this->gradeLevel->id,
            'section_id' => $this->section->id,
            'name' => '6ème B',
            'code' => '6EA',
            'capacity' => 30,
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_same_name_allowed_in_different_years(): void
    {
        DB::table('academic_years')->insert([
            'school_id' => $this->schoolId,
            'name' => '2026-2027',
            'code' => 'AY-2026-2027',
            'start_date' => '2026-09-01',
            'end_date' => '2027-07-31',
            'status' => 'active',
            'uuid' => Str::ulid(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $otherYear = AcademicYear::where('school_id', $this->schoolId)
            ->where('name', '2026-2027')
            ->first();

        AcademicClass::factory()
            ->forAcademicYear($this->academicYear->id)
            ->forProgram($this->program->id)
            ->forGradeLevel($this->gradeLevel->id)
            ->forSection($this->section->id)
            ->create(['name' => '6ème A', 'code' => '6EA']);

        $data = AcademicClassData::fromArray([
            'academic_year_id' => $otherYear->id,
            'program_id' => $this->program->id,
            'grade_level_id' => $this->gradeLevel->id,
            'section_id' => $this->section->id,
            'name' => '6ème A',
            'code' => '6EB',
            'capacity' => 30,
        ]);

        $academicClass = $this->service->create($data);

        $this->assertNotNull($academicClass);
        $this->assertEquals('6ème A', $academicClass->name);
    }

    public function test_capacity_must_be_greater_than_zero(): void
    {
        $this->expectException(ValidationException::class);

        $data = AcademicClassData::fromArray([
            'academic_year_id' => $this->academicYear->id,
            'program_id' => $this->program->id,
            'grade_level_id' => $this->gradeLevel->id,
            'section_id' => $this->section->id,
            'name' => '6ème A',
            'code' => '6EA',
            'capacity' => 0,
        ]);

        $this->service->create($data);
    }

    public function test_can_paginate_academic_classes(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            AcademicClass::factory()
                ->forAcademicYear($this->academicYear->id)
                ->forProgram($this->program->id)
                ->forGradeLevel($this->gradeLevel->id)
                ->forSection($this->section->id)
                ->withOrder($i)
                ->create();
        }

        $result = $this->service->paginate([], 10);

        $this->assertEquals(5, $result->total());
    }

    public function test_can_filter_by_status(): void
    {
        for ($i = 1; $i <= 3; $i++) {
            AcademicClass::factory()
                ->forAcademicYear($this->academicYear->id)
                ->forProgram($this->program->id)
                ->forGradeLevel($this->gradeLevel->id)
                ->forSection($this->section->id)
                ->active()
                ->withOrder($i)
                ->create();
        }
        AcademicClass::factory()
            ->forAcademicYear($this->academicYear->id)
            ->forProgram($this->program->id)
            ->forGradeLevel($this->gradeLevel->id)
            ->forSection($this->section->id)
            ->archived()
            ->withOrder(4)
            ->create();

        $result = $this->service->paginate(['status' => AcademicClassStatus::Active->value], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_can_search_by_name(): void
    {
        AcademicClass::factory()
            ->forAcademicYear($this->academicYear->id)
            ->forProgram($this->program->id)
            ->forGradeLevel($this->gradeLevel->id)
            ->forSection($this->section->id)
            ->withOrder(1)
            ->create(['name' => '6ème A', 'code' => '6EA']);
        AcademicClass::factory()
            ->forAcademicYear($this->academicYear->id)
            ->forProgram($this->program->id)
            ->forGradeLevel($this->gradeLevel->id)
            ->forSection($this->section->id)
            ->withOrder(2)
            ->create(['name' => '5ème A', 'code' => '5EA']);

        $result = $this->service->paginate(['search' => '6ème'], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_can_search_by_code(): void
    {
        AcademicClass::factory()
            ->forAcademicYear($this->academicYear->id)
            ->forProgram($this->program->id)
            ->forGradeLevel($this->gradeLevel->id)
            ->forSection($this->section->id)
            ->withOrder(1)
            ->create(['name' => '6ème A', 'code' => '6EA']);
        AcademicClass::factory()
            ->forAcademicYear($this->academicYear->id)
            ->forProgram($this->program->id)
            ->forGradeLevel($this->gradeLevel->id)
            ->forSection($this->section->id)
            ->withOrder(2)
            ->create(['name' => '5ème A', 'code' => '5EA']);

        $result = $this->service->paginate(['search' => '6EA'], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_can_filter_by_academic_year(): void
    {
        DB::table('academic_years')->insert([
            'school_id' => $this->schoolId,
            'name' => '2026-2027',
            'code' => 'AY-2026-2027',
            'start_date' => '2026-09-01',
            'end_date' => '2027-07-31',
            'status' => 'active',
            'uuid' => Str::ulid(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $otherYear = AcademicYear::where('school_id', $this->schoolId)
            ->where('name', '2026-2027')
            ->first();

        for ($i = 1; $i <= 3; $i++) {
            AcademicClass::factory()
                ->forAcademicYear($this->academicYear->id)
                ->forProgram($this->program->id)
                ->forGradeLevel($this->gradeLevel->id)
                ->forSection($this->section->id)
                ->withOrder($i)
                ->create();
        }
        for ($i = 1; $i <= 2; $i++) {
            AcademicClass::factory()
                ->forAcademicYear($otherYear->id)
                ->forProgram($this->program->id)
                ->forGradeLevel($this->gradeLevel->id)
                ->forSection($this->section->id)
                ->withOrder($i)
                ->create();
        }

        $result = $this->service->paginate(['academic_year_id' => $this->academicYear->id], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_can_filter_by_program(): void
    {
        $otherProgram = Program::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Senior High', 'code' => 'SHS']);

        for ($i = 1; $i <= 2; $i++) {
            AcademicClass::factory()
                ->forAcademicYear($this->academicYear->id)
                ->forProgram($this->program->id)
                ->forGradeLevel($this->gradeLevel->id)
                ->forSection($this->section->id)
                ->withOrder($i)
                ->create();
        }
        AcademicClass::factory()
            ->forAcademicYear($this->academicYear->id)
            ->forProgram($otherProgram->id)
            ->forGradeLevel($this->gradeLevel->id)
            ->forSection($this->section->id)
            ->withOrder(3)
            ->create();

        $result = $this->service->paginate(['program_id' => $this->program->id], 10);

        $this->assertEquals(2, $result->total());
    }

    public function test_events_dispatched_on_create(): void
    {
        Event::fake([AcademicClassCreated::class]);

        $data = AcademicClassData::fromArray([
            'academic_year_id' => $this->academicYear->id,
            'program_id' => $this->program->id,
            'grade_level_id' => $this->gradeLevel->id,
            'section_id' => $this->section->id,
            'name' => '6ème A',
            'code' => '6EA',
            'capacity' => 30,
        ]);

        $this->service->create($data);

        Event::assertDispatched(AcademicClassCreated::class);
    }

    public function test_events_dispatched_on_update(): void
    {
        $academicClass = AcademicClass::factory()
            ->forAcademicYear($this->academicYear->id)
            ->forProgram($this->program->id)
            ->forGradeLevel($this->gradeLevel->id)
            ->forSection($this->section->id)
            ->create();

        Event::fake([AcademicClassUpdated::class]);

        $this->service->update($academicClass, ['name' => 'Updated']);

        Event::assertDispatched(AcademicClassUpdated::class);
    }

    public function test_events_dispatched_on_archive(): void
    {
        $academicClass = AcademicClass::factory()
            ->forAcademicYear($this->academicYear->id)
            ->forProgram($this->program->id)
            ->forGradeLevel($this->gradeLevel->id)
            ->forSection($this->section->id)
            ->active()
            ->create();

        Event::fake([AcademicClassArchived::class]);

        $this->service->archive($academicClass);

        Event::assertDispatched(AcademicClassArchived::class);
    }

    public function test_events_dispatched_on_activate(): void
    {
        $academicClass = AcademicClass::factory()
            ->forAcademicYear($this->academicYear->id)
            ->forProgram($this->program->id)
            ->forGradeLevel($this->gradeLevel->id)
            ->forSection($this->section->id)
            ->archived()
            ->create();

        Event::fake([AcademicClassActivated::class]);

        $this->service->activate($academicClass);

        Event::assertDispatched(AcademicClassActivated::class);
    }

    public function test_can_find_by_id(): void
    {
        $academicClass = AcademicClass::factory()
            ->forAcademicYear($this->academicYear->id)
            ->forProgram($this->program->id)
            ->forGradeLevel($this->gradeLevel->id)
            ->forSection($this->section->id)
            ->create();

        $found = $this->service->findById($academicClass->id);

        $this->assertEquals($academicClass->id, $found->id);
    }

    public function test_find_by_id_throws_when_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->findById(99999);
    }

    public function test_update_validates_uniqueness_per_year(): void
    {
        AcademicClass::factory()
            ->forAcademicYear($this->academicYear->id)
            ->forProgram($this->program->id)
            ->forGradeLevel($this->gradeLevel->id)
            ->forSection($this->section->id)
            ->create(['name' => '6ème A', 'code' => '6EA']);

        $class2 = AcademicClass::factory()
            ->forAcademicYear($this->academicYear->id)
            ->forProgram($this->program->id)
            ->forGradeLevel($this->gradeLevel->id)
            ->forSection($this->section->id)
            ->create(['name' => '6ème B', 'code' => '6EB']);

        $this->expectException(ValidationException::class);

        $this->service->update($class2, ['name' => '6ème A']);
    }

    public function test_can_update_same_academic_class_name(): void
    {
        $academicClass = AcademicClass::factory()
            ->forAcademicYear($this->academicYear->id)
            ->forProgram($this->program->id)
            ->forGradeLevel($this->gradeLevel->id)
            ->forSection($this->section->id)
            ->create(['name' => '6ème A', 'code' => '6EA']);

        $updated = $this->service->update($academicClass, ['description' => 'Updated']);

        $this->assertEquals('6ème A', $updated->name);
        $this->assertEquals('Updated', $updated->description);
    }

    public function test_uuid_is_auto_generated(): void
    {
        $data = AcademicClassData::fromArray([
            'academic_year_id' => $this->academicYear->id,
            'program_id' => $this->program->id,
            'grade_level_id' => $this->gradeLevel->id,
            'section_id' => $this->section->id,
            'name' => '6ème A',
            'code' => '6EA',
            'capacity' => 30,
        ]);

        $academicClass = $this->service->create($data);

        $this->assertNotEmpty($academicClass->uuid);
    }

    public function test_status_enum_is_cast(): void
    {
        $data = AcademicClassData::fromArray([
            'academic_year_id' => $this->academicYear->id,
            'program_id' => $this->program->id,
            'grade_level_id' => $this->gradeLevel->id,
            'section_id' => $this->section->id,
            'name' => '6ème A',
            'code' => '6EA',
            'capacity' => 30,
        ]);

        $academicClass = $this->service->create($data);

        $this->assertInstanceOf(AcademicClassStatus::class, $academicClass->status);
    }

    public function test_is_active_defaults_to_true(): void
    {
        $data = AcademicClassData::fromArray([
            'academic_year_id' => $this->academicYear->id,
            'program_id' => $this->program->id,
            'grade_level_id' => $this->gradeLevel->id,
            'section_id' => $this->section->id,
            'name' => '6ème A',
            'code' => '6EA',
            'capacity' => 30,
        ]);

        $academicClass = $this->service->create($data);

        $this->assertTrue($academicClass->is_active);
    }

    public function test_capacity_must_be_at_least_one(): void
    {
        $this->expectException(ValidationException::class);

        $data = AcademicClassData::fromArray([
            'academic_year_id' => $this->academicYear->id,
            'program_id' => $this->program->id,
            'grade_level_id' => $this->gradeLevel->id,
            'section_id' => $this->section->id,
            'name' => '6ème A',
            'code' => '6EA',
            'capacity' => -5,
        ]);

        $this->service->create($data);
    }
}
