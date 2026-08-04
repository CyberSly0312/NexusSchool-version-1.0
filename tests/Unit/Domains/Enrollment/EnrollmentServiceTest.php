<?php

declare(strict_types=1);

use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\Enrollment\Application\DTOs\CreateEnrollmentData;
use App\Domains\Enrollment\Application\DTOs\GraduationData;
use App\Domains\Enrollment\Application\DTOs\PromotionData;
use App\Domains\Enrollment\Application\DTOs\TransferData;
use App\Domains\Enrollment\Application\DTOs\UpdateEnrollmentData;
use App\Domains\Enrollment\Application\Services\EnrollmentService;
use App\Domains\Enrollment\Domain\Enums\EnrollmentStatus;
use App\Domains\Enrollment\Domain\Enums\EnrollmentType;
use App\Domains\Enrollment\Domain\Enums\GraduationDecision;
use App\Domains\Enrollment\Domain\Enums\PromotionDecision;
use App\Domains\Enrollment\Domain\Enums\TransferType;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\Student\Infrastructure\Models\Student;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    [$this->year, $this->class, $this->program, $this->level] = makeEnrollmentContext($this->schoolId);
    $this->student = Student::factory()->create(['school_id' => $this->schoolId]);
    $this->service = app(EnrollmentService::class);

    $this->enrollment = $this->service->enroll(CreateEnrollmentData::fromArray([
        'school_id' => $this->schoolId,
        'student_id' => $this->student->id,
        'academic_calendar_year_id' => $this->year->id,
        'academic_program_id' => $this->program->id,
        'education_level_id' => $this->level->id,
        'school_class_id' => $this->class->id,
        'enrolled_at' => '2026-09-15',
    ]));
});

test('enroll creates an enrollment through the service', function (): void {
    $student = Student::factory()->create(['school_id' => $this->schoolId]);

    $enrollment = $this->service->enroll(CreateEnrollmentData::fromArray([
        'school_id' => $this->schoolId,
        'student_id' => $student->id,
        'academic_calendar_year_id' => $this->year->id,
        'academic_program_id' => $this->program->id,
        'education_level_id' => $this->level->id,
        'school_class_id' => $this->class->id,
        'enrolled_at' => '2026-09-20',
    ]));

    expect($enrollment)->toBeInstanceOf(Enrollment::class)
        ->and($enrollment->status)->toBe(EnrollmentStatus::Active);
});

test('update changes mutable metadata', function (): void {
    $updated = $this->service->update($this->enrollment, new UpdateEnrollmentData(notes: 'Validée'));

    expect($updated->fresh()->notes)->toBe('Validée');
});

test('promote creates the next enrollment row', function (): void {
    $nextYear = makeNextAcademicYear($this->schoolId, $this->year);

    $nextClass = SchoolClass::create([
        'school_id' => $this->schoolId,
        'academic_program_id' => $this->program->id,
        'education_level_id' => $this->level->id,
        'name' => '5ème A',
        'code' => 'CL5A',
        'status' => 'active',
    ]);

    $this->service->promote($this->enrollment, new PromotionData(
        decision: PromotionDecision::Promoted,
        academicCalendarYearId: $nextYear->id,
        promotedOn: '2027-09-15',
        toClassId: $nextClass->id,
    ));

    expect($this->enrollment->fresh()->status)->toBe(EnrollmentStatus::Archived);

    $next = Enrollment::forSchool($this->schoolId)->forYear($nextYear->id)->first();

    expect($next->enrollment_type)->toBe(EnrollmentType::Promotion)
        ->and($next->school_class_id)->toBe($nextClass->id);
});

test('repeat creates a repetition row', function (): void {
    $nextYear = makeNextAcademicYear($this->schoolId, $this->year);

    $this->service->repeat($this->enrollment, new PromotionData(
        decision: PromotionDecision::Repeated,
        academicCalendarYearId: $nextYear->id,
        promotedOn: '2027-09-15',
    ));

    $next = Enrollment::forSchool($this->schoolId)->forYear($nextYear->id)->first();

    expect($next->enrollment_type)->toBe(EnrollmentType::Repetition)
        ->and($next->school_class_id)->toBe($this->class->id);
});

test('transfer keeps the row active for internal moves', function (): void {
    $otherClass = SchoolClass::create([
        'school_id' => $this->schoolId,
        'academic_program_id' => $this->program->id,
        'education_level_id' => $this->level->id,
        'name' => '5ème B',
        'code' => 'CL5B',
        'status' => 'active',
    ]);

    $result = $this->service->transfer($this->enrollment, new TransferData(
        transferType: TransferType::Internal,
        transferDate: '2026-11-15',
        toClassId: $otherClass->id,
    ));

    expect($result->fresh()->status)->toBe(EnrollmentStatus::Active)
        ->and($result->fresh()->school_class_id)->toBe($otherClass->id);
});

test('graduate closes the enrollment', function (): void {
    $result = $this->service->graduate($this->enrollment, new GraduationData(
        decision: GraduationDecision::Graduated,
        educationLevelId: $this->level->id,
        graduatedOn: '2027-07-01',
    ));

    expect($result->fresh()->status)->toBe(EnrollmentStatus::Graduated);
});

test('withdraw closes the enrollment', function (): void {
    $result = $this->service->withdraw($this->enrollment, 'Déménagement');

    expect($result->fresh()->status)->toBe(EnrollmentStatus::Withdrawn)
        ->and($result->fresh()->exit_reason)->toBe('Déménagement');
});

test('suspend and restore round-trip', function (): void {
    $suspended = $this->service->suspend($this->enrollment, 'Retard');

    expect($suspended->fresh()->status)->toBe(EnrollmentStatus::Suspended);

    $restored = $this->service->restore($suspended->fresh());

    expect($restored->fresh()->status)->toBe(EnrollmentStatus::Active);
});

test('paginate filters by school', function (): void {
    $otherSchool = makeTenant();
    Enrollment::factory()->count(3)->create(['school_id' => $otherSchool]);

    expect($this->service->paginate(['school_id' => $this->schoolId], 10)->total())->toBe(1)
        ->and($this->service->paginate(['school_id' => $otherSchool], 10)->total())->toBe(3);
});

test('paginate filters by status', function (): void {
    Enrollment::factory()->active()->count(2)->create(['school_id' => $this->schoolId]);
    Enrollment::factory()->withdrawn()->create(['school_id' => $this->schoolId]);

    $result = $this->service->paginate(['school_id' => $this->schoolId, 'status' => EnrollmentStatus::Active->value], 10);

    expect($result->total())->toBe(3);
});

test('paginate filters by student and year', function (): void {
    $otherStudent = Student::factory()->create(['school_id' => $this->schoolId]);
    Enrollment::factory()->create(['school_id' => $this->schoolId, 'student_id' => $this->student->id]);
    Enrollment::factory()->create(['school_id' => $this->schoolId, 'student_id' => $otherStudent->id]);

    expect($this->service->paginate(['school_id' => $this->schoolId, 'student_id' => $this->student->id], 10)->total())->toBe(2)
        ->and($this->service->paginate(['school_id' => $this->schoolId, 'academic_calendar_year_id' => $this->year->id], 10)->total())->toBe(1);
});

test('paginate searches by enrollment number', function (): void {
    Enrollment::factory()->create(['school_id' => $this->schoolId, 'enrollment_number' => 'ENR-SPECIAL-0001']);
    Enrollment::factory()->count(3)->create(['school_id' => $this->schoolId]);

    $result = $this->service->paginate(['school_id' => $this->schoolId, 'search' => 'SPECIAL'], 10);

    expect($result->total())->toBe(1);
});

test('paginate searches by student name', function (): void {
    $kwame = Student::factory()->create(['school_id' => $this->schoolId, 'first_name' => 'Kwame', 'last_name' => 'Asante']);
    Enrollment::factory()->create(['school_id' => $this->schoolId, 'student_id' => $kwame->id]);
    Enrollment::factory()->count(3)->create(['school_id' => $this->schoolId]);

    $result = $this->service->paginate(['school_id' => $this->schoolId, 'search' => 'Kwame'], 10);

    expect($result->total())->toBe(1);
});
