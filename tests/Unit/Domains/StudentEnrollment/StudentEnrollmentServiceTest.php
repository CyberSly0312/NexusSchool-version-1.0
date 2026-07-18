<?php

namespace Tests\Unit\Domains\StudentEnrollment;

use App\Domains\StudentEnrollment\DTOs\StudentEnrollmentData;
use App\Domains\StudentEnrollment\Enums\EnrollmentStatus;
use App\Domains\StudentEnrollment\Enums\EnrollmentType;
use App\Domains\StudentEnrollment\Events\EnrollmentActivated;
use App\Domains\StudentEnrollment\Events\EnrollmentCompleted;
use App\Domains\StudentEnrollment\Events\EnrollmentSuspended;
use App\Domains\StudentEnrollment\Events\EnrollmentWithdrawn;
use App\Domains\StudentEnrollment\Events\StudentEnrolled;
use App\Domains\StudentEnrollment\Services\StudentEnrollmentService;
use App\Models\AcademicClass;
use App\Models\AcademicYear;
use App\Models\GradeLevel;
use App\Models\Program;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class StudentEnrollmentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StudentEnrollmentService $service;

    protected string $schoolId;

    protected Student $student;

    protected AcademicYear $academicYear;

    protected AcademicClass $academicClass;

    protected Program $program;

    protected GradeLevel $gradeLevel;

    protected Section $section;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new StudentEnrollmentService;

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

        $this->student = Student::factory()
            ->forSchool($this->schoolId)
            ->create(['status' => 'active']);

        $this->academicYear = AcademicYear::factory()
            ->create(['school_id' => $this->schoolId]);

        $this->program = Program::factory()
            ->create(['school_id' => $this->schoolId]);

        $this->gradeLevel = GradeLevel::factory()
            ->create(['program_id' => $this->program->id]);

        $this->section = Section::factory()
            ->create(['grade_level_id' => $this->gradeLevel->id]);

        $this->academicClass = AcademicClass::factory()
            ->create([
                'academic_year_id' => $this->academicYear->id,
                'program_id' => $this->program->id,
                'grade_level_id' => $this->gradeLevel->id,
                'section_id' => $this->section->id,
            ]);
    }

    private function makeEnrollmentData(array $overrides = []): StudentEnrollmentData
    {
        return StudentEnrollmentData::fromArray(array_merge([
            'school_id' => $this->schoolId,
            'student_id' => $this->student->id,
            'academic_year_id' => $this->academicYear->id,
            'academic_class_id' => $this->academicClass->id,
            'program_id' => $this->program->id,
            'grade_level_id' => $this->gradeLevel->id,
            'section_id' => $this->section->id,
            'enrollment_number' => 'ENR-001',
            'enrollment_date' => '2025-01-15',
            'effective_date' => '2025-01-15',
            'enrollment_type' => 'new_admission',
        ], $overrides));
    }

    public function test_can_create_enrollment(): void
    {
        $enrollment = $this->service->create($this->makeEnrollmentData());

        $this->assertInstanceOf(StudentEnrollment::class, $enrollment);
        $this->assertEquals('ENR-001', $enrollment->enrollment_number);
        $this->assertEquals($this->student->id, $enrollment->student_id);
        $this->assertEquals(EnrollmentStatus::Pending, $enrollment->status);
        $this->assertEquals(EnrollmentType::NewAdmission, $enrollment->enrollment_type);
    }

    public function test_can_update_enrollment(): void
    {
        $enrollment = $this->service->create($this->makeEnrollmentData());

        $updated = $this->service->update($enrollment, ['notes' => 'Updated notes']);

        $this->assertEquals('Updated notes', $updated->notes);
    }

    public function test_can_delete_enrollment(): void
    {
        $enrollment = $this->service->create($this->makeEnrollmentData());

        $deleted = $this->service->delete($enrollment);

        $this->assertTrue($deleted);
        $this->assertSoftDeleted('student_enrollments', ['id' => $enrollment->id]);
    }

    public function test_can_activate_enrollment(): void
    {
        $enrollment = $this->service->create($this->makeEnrollmentData());

        $activated = $this->service->activate($enrollment);

        $this->assertEquals(EnrollmentStatus::Active, $activated->status);
    }

    public function test_can_suspend_enrollment(): void
    {
        $enrollment = $this->service->create($this->makeEnrollmentData());
        $enrollment = $this->service->activate($enrollment);

        $suspended = $this->service->suspend($enrollment);

        $this->assertEquals(EnrollmentStatus::Suspended, $suspended->status);
    }

    public function test_can_complete_enrollment(): void
    {
        $enrollment = $this->service->create($this->makeEnrollmentData());
        $enrollment = $this->service->activate($enrollment);

        $completed = $this->service->complete($enrollment);

        $this->assertEquals(EnrollmentStatus::Completed, $completed->status);
        $this->assertNotNull($completed->exit_date);
    }

    public function test_can_withdraw_enrollment(): void
    {
        $enrollment = $this->service->create($this->makeEnrollmentData());
        $enrollment = $this->service->activate($enrollment);

        $withdrawn = $this->service->withdraw($enrollment, 'Family relocation');

        $this->assertEquals(EnrollmentStatus::Withdrawn, $withdrawn->status);
        $this->assertEquals('Family relocation', $withdrawn->exit_reason);
    }

    public function test_duplicate_enrollment_number_is_rejected(): void
    {
        $this->service->create($this->makeEnrollmentData());

        $this->expectException(ValidationException::class);

        $this->service->create($this->makeEnrollmentData());
    }

    public function test_same_enrollment_number_allowed_in_different_schools(): void
    {
        $this->service->create($this->makeEnrollmentData());

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

        $otherStudent = Student::factory()
            ->forSchool($otherSchoolId)
            ->create(['status' => 'active']);

        $otherAcademicYear = AcademicYear::factory()
            ->create(['school_id' => $otherSchoolId]);

        $otherProgram = Program::factory()
            ->create(['school_id' => $otherSchoolId]);

        $otherGradeLevel = GradeLevel::factory()
            ->create(['program_id' => $otherProgram->id]);

        $otherSection = Section::factory()
            ->create(['grade_level_id' => $otherGradeLevel->id]);

        $otherClass = AcademicClass::factory()
            ->create([
                'academic_year_id' => $otherAcademicYear->id,
                'program_id' => $otherProgram->id,
                'grade_level_id' => $otherGradeLevel->id,
                'section_id' => $otherSection->id,
            ]);

        $data = $this->makeEnrollmentData([
            'school_id' => $otherSchoolId,
            'student_id' => $otherStudent->id,
            'academic_year_id' => $otherAcademicYear->id,
            'academic_class_id' => $otherClass->id,
            'program_id' => $otherProgram->id,
            'grade_level_id' => $otherGradeLevel->id,
            'section_id' => $otherSection->id,
        ]);

        $enrollment = $this->service->create($data);

        $this->assertNotNull($enrollment);
    }

    public function test_duplicate_active_enrollment_in_same_year_is_rejected(): void
    {
        $this->service->create($this->makeEnrollmentData());
        $this->service->activate($this->service->findById(1));

        $this->expectException(ValidationException::class);

        $this->service->create($this->makeEnrollmentData(['enrollment_number' => 'ENR-002']));
    }

    public function test_inactive_student_enrollment_is_rejected(): void
    {
        $inactiveStudent = Student::factory()
            ->forSchool($this->schoolId)
            ->create(['status' => 'archived']);

        $this->expectException(ValidationException::class);

        $this->service->create($this->makeEnrollmentData([
            'student_id' => $inactiveStudent->id,
            'enrollment_number' => 'ENR-002',
        ]));
    }

    public function test_invalid_lifecycle_transition_is_rejected(): void
    {
        $enrollment = $this->service->create($this->makeEnrollmentData());

        $this->expectException(ValidationException::class);

        $this->service->complete($enrollment);
    }

    public function test_completed_enrollment_cannot_transition(): void
    {
        $enrollment = $this->service->create($this->makeEnrollmentData());
        $enrollment = $this->service->activate($enrollment);
        $enrollment = $this->service->complete($enrollment);

        $this->expectException(ValidationException::class);

        $this->service->suspend($enrollment);
    }

    public function test_withdrawn_enrollment_cannot_transition(): void
    {
        $enrollment = $this->service->create($this->makeEnrollmentData());
        $enrollment = $this->service->activate($enrollment);
        $enrollment = $this->service->withdraw($enrollment);

        $this->expectException(ValidationException::class);

        $this->service->activate($enrollment);
    }

    public function test_cancelled_enrollment_cannot_transition(): void
    {
        $enrollment = $this->service->create($this->makeEnrollmentData());

        // Can only transition from pending to cancelled or active
        // No cancel method in service, so we update directly
        $enrollment->update(['status' => EnrollmentStatus::Cancelled]);
        $enrollment->refresh();

        $this->expectException(ValidationException::class);

        $this->service->activate($enrollment);
    }

    public function test_archived_enrollments_are_read_only(): void
    {
        $enrollment = $this->service->create($this->makeEnrollmentData());
        $enrollment = $this->service->activate($enrollment);
        $enrollment = $this->service->complete($enrollment);

        $this->expectException(ValidationException::class);

        $this->service->update($enrollment, ['notes' => 'Trying to modify']);
    }

    public function test_invalid_enrollment_type_is_rejected(): void
    {
        $data = [
            'school_id' => $this->schoolId,
            'student_id' => $this->student->id,
            'academic_year_id' => $this->academicYear->id,
            'academic_class_id' => $this->academicClass->id,
            'program_id' => $this->program->id,
            'grade_level_id' => $this->gradeLevel->id,
            'section_id' => $this->section->id,
            'enrollment_number' => 'ENR-002',
            'enrollment_date' => '2025-01-15',
            'effective_date' => '2025-01-15',
            'enrollment_type' => 'invalid_type',
        ];

        $this->expectException(ValidationException::class);

        $this->service->create(StudentEnrollmentData::fromArray($data));
    }

    public function test_invalid_status_is_rejected(): void
    {
        $data = [
            'school_id' => $this->schoolId,
            'student_id' => $this->student->id,
            'academic_year_id' => $this->academicYear->id,
            'academic_class_id' => $this->academicClass->id,
            'program_id' => $this->program->id,
            'grade_level_id' => $this->gradeLevel->id,
            'section_id' => $this->section->id,
            'enrollment_number' => 'ENR-002',
            'enrollment_date' => '2025-01-15',
            'effective_date' => '2025-01-15',
            'enrollment_type' => 'new_admission',
            'status' => 'invalid_status',
        ];

        $this->expectException(ValidationException::class);

        $this->service->create(StudentEnrollmentData::fromArray($data));
    }

    public function test_can_paginate_enrollments(): void
    {
        $this->service->create($this->makeEnrollmentData());
        $this->service->create($this->makeEnrollmentData([
            'enrollment_number' => 'ENR-002',
            'enrollment_date' => '2025-02-01',
            'effective_date' => '2025-02-01',
        ]));

        $result = $this->service->paginate([], 10);

        $this->assertEquals(2, $result->total());
    }

    public function test_can_filter_by_status(): void
    {
        $this->service->create($this->makeEnrollmentData());
        $act = $this->service->create($this->makeEnrollmentData([
            'enrollment_number' => 'ENR-002',
            'enrollment_date' => '2025-02-01',
            'effective_date' => '2025-02-01',
        ]));
        $this->service->activate($act);

        $result = $this->service->paginate(['status' => EnrollmentStatus::Pending->value], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_can_filter_by_enrollment_type(): void
    {
        $this->service->create($this->makeEnrollmentData());
        $this->service->create($this->makeEnrollmentData([
            'enrollment_number' => 'ENR-002',
            'enrollment_date' => '2025-02-01',
            'effective_date' => '2025-02-01',
            'enrollment_type' => 'transfer_in',
        ]));

        $result = $this->service->paginate(['enrollment_type' => 'transfer_in'], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_can_filter_by_school(): void
    {
        $this->service->create($this->makeEnrollmentData());

        $result = $this->service->paginate(['school_id' => 'non-existent'], 10);

        $this->assertEquals(0, $result->total());
    }

    public function test_can_search_by_enrollment_number(): void
    {
        $this->service->create($this->makeEnrollmentData(['enrollment_number' => 'ENR-SPECIAL']));

        $result = $this->service->paginate(['search' => 'SPECIAL'], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_events_dispatched_on_create(): void
    {
        Event::fake([StudentEnrolled::class]);

        $this->service->create($this->makeEnrollmentData());

        Event::assertDispatched(StudentEnrolled::class);
    }

    public function test_events_dispatched_on_activate(): void
    {
        $enrollment = $this->service->create($this->makeEnrollmentData());

        Event::fake([EnrollmentActivated::class]);

        $this->service->activate($enrollment);

        Event::assertDispatched(EnrollmentActivated::class);
    }

    public function test_events_dispatched_on_suspend(): void
    {
        $enrollment = $this->service->create($this->makeEnrollmentData());
        $enrollment = $this->service->activate($enrollment);

        Event::fake([EnrollmentSuspended::class]);

        $this->service->suspend($enrollment);

        Event::assertDispatched(EnrollmentSuspended::class);
    }

    public function test_events_dispatched_on_complete(): void
    {
        $enrollment = $this->service->create($this->makeEnrollmentData());
        $enrollment = $this->service->activate($enrollment);

        Event::fake([EnrollmentCompleted::class]);

        $this->service->complete($enrollment);

        Event::assertDispatched(EnrollmentCompleted::class);
    }

    public function test_events_dispatched_on_withdraw(): void
    {
        $enrollment = $this->service->create($this->makeEnrollmentData());
        $enrollment = $this->service->activate($enrollment);

        Event::fake([EnrollmentWithdrawn::class]);

        $this->service->withdraw($enrollment);

        Event::assertDispatched(EnrollmentWithdrawn::class);
    }

    public function test_can_find_by_id(): void
    {
        $enrollment = $this->service->create($this->makeEnrollmentData());

        $found = $this->service->findById($enrollment->id);

        $this->assertEquals($enrollment->id, $found->id);
    }

    public function test_find_by_id_throws_when_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->findById(99999);
    }

    public function test_uuid_is_auto_generated(): void
    {
        $enrollment = $this->service->create($this->makeEnrollmentData());

        $this->assertNotEmpty($enrollment->uuid);
    }

    public function test_can_unsuspend_enrollment(): void
    {
        $enrollment = $this->service->create($this->makeEnrollmentData());
        $enrollment = $this->service->activate($enrollment);
        $enrollment = $this->service->suspend($enrollment);

        $activated = $this->service->activate($enrollment);

        $this->assertEquals(EnrollmentStatus::Active, $activated->status);
    }

    public function test_get_active_enrollments(): void
    {
        $enrollment = $this->service->create($this->makeEnrollmentData());
        $this->service->activate($enrollment);

        $active = $this->service->getActiveEnrollments($this->student->id, $this->academicYear->id);

        $this->assertNotNull($active);
        $this->assertEquals($enrollment->id, $active->id);
    }

    public function test_get_active_enrollments_returns_null_when_none(): void
    {
        $active = $this->service->getActiveEnrollments($this->student->id, $this->academicYear->id);

        $this->assertNull($active);
    }
}
