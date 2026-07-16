<?php

namespace Tests\Unit\Domains\SubjectCoefficient;

use App\Domains\SubjectCoefficient\DTOs\SubjectCoefficientData;
use App\Domains\SubjectCoefficient\Enums\SubjectCoefficientStatus;
use App\Domains\SubjectCoefficient\Events\SubjectCoefficientActivated;
use App\Domains\SubjectCoefficient\Events\SubjectCoefficientArchived;
use App\Domains\SubjectCoefficient\Events\SubjectCoefficientCreated;
use App\Domains\SubjectCoefficient\Events\SubjectCoefficientUpdated;
use App\Domains\SubjectCoefficient\Services\SubjectCoefficientService;
use App\Models\GradeLevel;
use App\Models\Program;
use App\Models\Subject;
use App\Models\SubjectCategory;
use App\Models\SubjectCoefficient;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SubjectCoefficientServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SubjectCoefficientService $service;

    protected string $schoolId;

    protected int $subjectId;

    protected int $programId;

    protected int $gradeLevelId;

    protected ?int $academicYearId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new SubjectCoefficientService;

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

        $category = SubjectCategory::factory()->forSchool($schoolId)->create([
            'name' => 'General',
            'code' => 'GEN',
        ]);

        $subject = Subject::factory()
            ->forSchool($schoolId)
            ->forCategory($category->id)
            ->create(['name' => 'Mathematics', 'code' => 'MATH101']);

        $this->subjectId = $subject->id;

        $program = Program::factory()
            ->forSchool($schoolId)
            ->create(['name' => 'Science Program', 'code' => 'SCI']);

        $this->programId = $program->id;

        $gradeLevel = GradeLevel::factory()
            ->forProgram($program->id)
            ->create(['name' => 'Grade 10', 'code' => 'G10']);

        $this->gradeLevelId = $gradeLevel->id;

        $this->academicYearId = null;
    }

    public function test_can_create_subject_coefficient(): void
    {
        $data = SubjectCoefficientData::fromArray([
            'school_id' => $this->schoolId,
            'subject_id' => $this->subjectId,
            'program_id' => $this->programId,
            'grade_level_id' => $this->gradeLevelId,
            'academic_year_id' => null,
            'coefficient' => '1.50',
            'effective_from' => '2024-09-01',
            'effective_to' => '2025-06-30',
            'notes' => 'Standard coefficient',
        ]);

        $subjectCoefficient = $this->service->create($data);

        $this->assertInstanceOf(SubjectCoefficient::class, $subjectCoefficient);
        $this->assertEquals('1.50', $subjectCoefficient->coefficient);
        $this->assertEquals('2024-09-01', $subjectCoefficient->effective_from->format('Y-m-d'));
        $this->assertEquals('2025-06-30', $subjectCoefficient->effective_to->format('Y-m-d'));
        $this->assertEquals(SubjectCoefficientStatus::Active, $subjectCoefficient->status);
        $this->assertEquals('Standard coefficient', $subjectCoefficient->notes);
    }

    public function test_can_update_subject_coefficient(): void
    {
        $subjectCoefficient = SubjectCoefficient::factory()
            ->forSchool($this->schoolId)
            ->forSubject($this->subjectId)
            ->forProgram($this->programId)
            ->forGradeLevel($this->gradeLevelId)
            ->create(['coefficient' => '1.00']);

        $updated = $this->service->update($subjectCoefficient, ['coefficient' => '2.00']);

        $this->assertEquals('2.00', $updated->coefficient);
    }

    public function test_can_delete_subject_coefficient(): void
    {
        $subjectCoefficient = SubjectCoefficient::factory()
            ->forSchool($this->schoolId)
            ->forSubject($this->subjectId)
            ->forProgram($this->programId)
            ->forGradeLevel($this->gradeLevelId)
            ->create();

        $deleted = $this->service->delete($subjectCoefficient);

        $this->assertTrue($deleted);
        $this->assertSoftDeleted('subject_coefficients', ['id' => $subjectCoefficient->id]);
    }

    public function test_can_archive_subject_coefficient(): void
    {
        $subjectCoefficient = SubjectCoefficient::factory()
            ->forSchool($this->schoolId)
            ->forSubject($this->subjectId)
            ->forProgram($this->programId)
            ->forGradeLevel($this->gradeLevelId)
            ->active()
            ->create();

        $archived = $this->service->archive($subjectCoefficient);

        $this->assertEquals(SubjectCoefficientStatus::Archived, $archived->status);
    }

    public function test_can_activate_subject_coefficient(): void
    {
        $subjectCoefficient = SubjectCoefficient::factory()
            ->forSchool($this->schoolId)
            ->forSubject($this->subjectId)
            ->forProgram($this->programId)
            ->forGradeLevel($this->gradeLevelId)
            ->archived()
            ->create();

        $activated = $this->service->activate($subjectCoefficient);

        $this->assertEquals(SubjectCoefficientStatus::Active, $activated->status);
    }

    public function test_duplicate_active_coefficient_same_context_is_rejected(): void
    {
        SubjectCoefficient::factory()
            ->forSchool($this->schoolId)
            ->forSubject($this->subjectId)
            ->forProgram($this->programId)
            ->forGradeLevel($this->gradeLevelId)
            ->active()
            ->create(['coefficient' => '1.00']);

        $data = SubjectCoefficientData::fromArray([
            'school_id' => $this->schoolId,
            'subject_id' => $this->subjectId,
            'program_id' => $this->programId,
            'grade_level_id' => $this->gradeLevelId,
            'academic_year_id' => null,
            'coefficient' => '2.00',
            'effective_from' => '2024-09-01',
            'effective_to' => '2025-06-30',
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_same_context_allows_archived_duplicate(): void
    {
        SubjectCoefficient::factory()
            ->forSchool($this->schoolId)
            ->forSubject($this->subjectId)
            ->forProgram($this->programId)
            ->forGradeLevel($this->gradeLevelId)
            ->archived()
            ->create(['coefficient' => '1.00']);

        $data = SubjectCoefficientData::fromArray([
            'school_id' => $this->schoolId,
            'subject_id' => $this->subjectId,
            'program_id' => $this->programId,
            'grade_level_id' => $this->gradeLevelId,
            'academic_year_id' => null,
            'coefficient' => '2.00',
            'effective_from' => '2024-09-01',
            'effective_to' => '2025-06-30',
        ]);

        $subjectCoefficient = $this->service->create($data);

        $this->assertNotNull($subjectCoefficient);
    }

    public function test_overlapping_dates_are_rejected(): void
    {
        SubjectCoefficient::factory()
            ->forSchool($this->schoolId)
            ->forSubject($this->subjectId)
            ->forProgram($this->programId)
            ->forGradeLevel($this->gradeLevelId)
            ->active()
            ->withEffectiveFrom('2024-09-01')
            ->withEffectiveTo('2025-06-30')
            ->create(['coefficient' => '1.00']);

        $data = SubjectCoefficientData::fromArray([
            'school_id' => $this->schoolId,
            'subject_id' => $this->subjectId,
            'program_id' => $this->programId,
            'grade_level_id' => $this->gradeLevelId,
            'academic_year_id' => null,
            'coefficient' => '2.00',
            'effective_from' => '2025-01-01',
            'effective_to' => '2025-12-31',
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_coefficient_must_be_greater_than_zero(): void
    {
        $data = SubjectCoefficientData::fromArray([
            'school_id' => $this->schoolId,
            'subject_id' => $this->subjectId,
            'program_id' => $this->programId,
            'grade_level_id' => $this->gradeLevelId,
            'academic_year_id' => null,
            'coefficient' => '0',
            'effective_from' => '2024-09-01',
            'effective_to' => '2025-06-30',
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_can_paginate_subject_coefficients(): void
    {
        SubjectCoefficient::factory()
            ->forSchool($this->schoolId)
            ->forSubject($this->subjectId)
            ->forProgram($this->programId)
            ->forGradeLevel($this->gradeLevelId)
            ->count(5)
            ->create();

        $result = $this->service->paginate([], 10);

        $this->assertEquals(5, $result->total());
    }

    public function test_can_filter_by_status(): void
    {
        SubjectCoefficient::factory()
            ->forSchool($this->schoolId)
            ->forSubject($this->subjectId)
            ->forProgram($this->programId)
            ->forGradeLevel($this->gradeLevelId)
            ->active()
            ->count(3)
            ->create();
        SubjectCoefficient::factory()
            ->forSchool($this->schoolId)
            ->forSubject($this->subjectId)
            ->forProgram($this->programId)
            ->forGradeLevel($this->gradeLevelId)
            ->archived()
            ->create();

        $result = $this->service->paginate(['status' => SubjectCoefficientStatus::Active->value], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_can_filter_by_subject(): void
    {
        $subject2 = Subject::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Physics', 'code' => 'PHYS101']);

        SubjectCoefficient::factory()
            ->forSchool($this->schoolId)
            ->forSubject($this->subjectId)
            ->forProgram($this->programId)
            ->forGradeLevel($this->gradeLevelId)
            ->count(3)
            ->create();
        SubjectCoefficient::factory()
            ->forSchool($this->schoolId)
            ->forSubject($subject2->id)
            ->forProgram($this->programId)
            ->forGradeLevel($this->gradeLevelId)
            ->count(2)
            ->create();

        $result = $this->service->paginate(['subject_id' => $this->subjectId], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_can_filter_by_program(): void
    {
        $program2 = Program::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Arts Program', 'code' => 'ARTS']);

        SubjectCoefficient::factory()
            ->forSchool($this->schoolId)
            ->forSubject($this->subjectId)
            ->forProgram($this->programId)
            ->forGradeLevel($this->gradeLevelId)
            ->count(3)
            ->create();
        SubjectCoefficient::factory()
            ->forSchool($this->schoolId)
            ->forSubject($this->subjectId)
            ->forProgram($program2->id)
            ->forGradeLevel($this->gradeLevelId)
            ->count(2)
            ->create();

        $result = $this->service->paginate(['program_id' => $this->programId], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_can_filter_by_grade_level(): void
    {
        $gradeLevel2 = GradeLevel::factory()
            ->forProgram($this->programId)
            ->create(['name' => 'Grade 11', 'code' => 'G11']);

        SubjectCoefficient::factory()
            ->forSchool($this->schoolId)
            ->forSubject($this->subjectId)
            ->forProgram($this->programId)
            ->forGradeLevel($this->gradeLevelId)
            ->count(3)
            ->create();
        SubjectCoefficient::factory()
            ->forSchool($this->schoolId)
            ->forSubject($this->subjectId)
            ->forProgram($this->programId)
            ->forGradeLevel($gradeLevel2->id)
            ->count(2)
            ->create();

        $result = $this->service->paginate(['grade_level_id' => $this->gradeLevelId], 10);

        $this->assertEquals(3, $result->total());
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

        SubjectCoefficient::factory()
            ->forSchool($this->schoolId)
            ->forSubject($this->subjectId)
            ->forProgram($this->programId)
            ->forGradeLevel($this->gradeLevelId)
            ->count(3)
            ->create();
        SubjectCoefficient::factory()
            ->forSchool($otherSchoolId)
            ->forSubject($this->subjectId)
            ->forProgram($this->programId)
            ->forGradeLevel($this->gradeLevelId)
            ->count(2)
            ->create();

        $result = $this->service->paginate(['school_id' => $this->schoolId], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_events_dispatched_on_create(): void
    {
        Event::fake([SubjectCoefficientCreated::class]);

        $data = SubjectCoefficientData::fromArray([
            'school_id' => $this->schoolId,
            'subject_id' => $this->subjectId,
            'program_id' => $this->programId,
            'grade_level_id' => $this->gradeLevelId,
            'academic_year_id' => null,
            'coefficient' => '1.50',
            'effective_from' => '2024-09-01',
            'effective_to' => '2025-06-30',
        ]);

        $this->service->create($data);

        Event::assertDispatched(SubjectCoefficientCreated::class);
    }

    public function test_events_dispatched_on_update(): void
    {
        $subjectCoefficient = SubjectCoefficient::factory()
            ->forSchool($this->schoolId)
            ->forSubject($this->subjectId)
            ->forProgram($this->programId)
            ->forGradeLevel($this->gradeLevelId)
            ->create();

        Event::fake([SubjectCoefficientUpdated::class]);

        $this->service->update($subjectCoefficient, ['coefficient' => '2.00']);

        Event::assertDispatched(SubjectCoefficientUpdated::class);
    }

    public function test_events_dispatched_on_archive(): void
    {
        $subjectCoefficient = SubjectCoefficient::factory()
            ->forSchool($this->schoolId)
            ->forSubject($this->subjectId)
            ->forProgram($this->programId)
            ->forGradeLevel($this->gradeLevelId)
            ->active()
            ->create();

        Event::fake([SubjectCoefficientArchived::class]);

        $this->service->archive($subjectCoefficient);

        Event::assertDispatched(SubjectCoefficientArchived::class);
    }

    public function test_events_dispatched_on_activate(): void
    {
        $subjectCoefficient = SubjectCoefficient::factory()
            ->forSchool($this->schoolId)
            ->forSubject($this->subjectId)
            ->forProgram($this->programId)
            ->forGradeLevel($this->gradeLevelId)
            ->archived()
            ->create();

        Event::fake([SubjectCoefficientActivated::class]);

        $this->service->activate($subjectCoefficient);

        Event::assertDispatched(SubjectCoefficientActivated::class);
    }

    public function test_can_find_by_id(): void
    {
        $subjectCoefficient = SubjectCoefficient::factory()
            ->forSchool($this->schoolId)
            ->forSubject($this->subjectId)
            ->forProgram($this->programId)
            ->forGradeLevel($this->gradeLevelId)
            ->create();

        $found = $this->service->findById($subjectCoefficient->id);

        $this->assertEquals($subjectCoefficient->id, $found->id);
    }

    public function test_find_by_id_throws_when_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->findById(99999);
    }

    public function test_uuid_is_auto_generated(): void
    {
        $data = SubjectCoefficientData::fromArray([
            'school_id' => $this->schoolId,
            'subject_id' => $this->subjectId,
            'program_id' => $this->programId,
            'grade_level_id' => $this->gradeLevelId,
            'academic_year_id' => null,
            'coefficient' => '1.50',
            'effective_from' => '2024-09-01',
            'effective_to' => '2025-06-30',
        ]);

        $subjectCoefficient = $this->service->create($data);

        $this->assertNotEmpty($subjectCoefficient->uuid);
    }

    public function test_effective_to_is_nullable(): void
    {
        $data = SubjectCoefficientData::fromArray([
            'school_id' => $this->schoolId,
            'subject_id' => $this->subjectId,
            'program_id' => $this->programId,
            'grade_level_id' => $this->gradeLevelId,
            'academic_year_id' => null,
            'coefficient' => '1.50',
            'effective_from' => '2024-09-01',
            'effective_to' => null,
            'notes' => null,
        ]);

        $subjectCoefficient = $this->service->create($data);

        $this->assertNull($subjectCoefficient->effective_to);
        $this->assertNull($subjectCoefficient->notes);
    }

    public function test_notes_is_nullable(): void
    {
        $data = SubjectCoefficientData::fromArray([
            'school_id' => $this->schoolId,
            'subject_id' => $this->subjectId,
            'program_id' => $this->programId,
            'grade_level_id' => $this->gradeLevelId,
            'academic_year_id' => null,
            'coefficient' => '1.50',
            'effective_from' => '2024-09-01',
            'effective_to' => '2025-06-30',
            'notes' => null,
        ]);

        $subjectCoefficient = $this->service->create($data);

        $this->assertNull($subjectCoefficient->notes);
    }
}
