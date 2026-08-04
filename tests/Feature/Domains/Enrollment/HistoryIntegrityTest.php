<?php

declare(strict_types=1);

use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\Enrollment\Application\Actions\CreateEnrollmentAction;
use App\Domains\Enrollment\Application\Actions\PromoteStudentAction;
use App\Domains\Enrollment\Application\Actions\UpdateEnrollmentAction;
use App\Domains\Enrollment\Application\DTOs\CreateEnrollmentData;
use App\Domains\Enrollment\Application\DTOs\PromotionData;
use App\Domains\Enrollment\Application\DTOs\UpdateEnrollmentData;
use App\Domains\Enrollment\Domain\Enums\EnrollmentStatus;
use App\Domains\Enrollment\Domain\Enums\EnrollmentType;
use App\Domains\Enrollment\Domain\Enums\PromotionDecision;
use App\Domains\Enrollment\Domain\Exceptions\EnrollmentStateException;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\Enrollment\Infrastructure\Models\EnrollmentHistory;
use App\Domains\Student\Infrastructure\Models\Student;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    [$this->year, $this->class, $this->program, $this->level] = makeEnrollmentContext($this->schoolId);
    $this->student = Student::factory()->create(['school_id' => $this->schoolId]);
});

function historyEnrollmentData(object $context, object $student): CreateEnrollmentData
{
    return CreateEnrollmentData::fromArray([
        'school_id' => $context->schoolId,
        'student_id' => $student->id,
        'academic_calendar_year_id' => $context->year->id,
        'academic_program_id' => $context->program->id,
        'education_level_id' => $context->level->id,
        'school_class_id' => $context->class->id,
        'enrolled_at' => '2026-09-15',
    ]);
}

test('promotion creates a new row and archives the source as history', function (): void {
    $enrollment = app(CreateEnrollmentAction::class)->execute(historyEnrollmentData($this, $this->student));

    $nextYear = makeNextAcademicYear($this->schoolId, $this->year);

    $nextClass = SchoolClass::create([
        'school_id' => $this->schoolId,
        'academic_program_id' => $this->program->id,
        'education_level_id' => $this->level->id,
        'name' => '5ème A',
        'code' => 'CL5A',
        'status' => 'active',
    ]);

    app(PromoteStudentAction::class)->execute($enrollment, new PromotionData(
        decision: PromotionDecision::Promoted,
        academicCalendarYearId: $nextYear->id,
        promotedOn: '2027-09-15',
        toClassId: $nextClass->id,
    ));

    expect(Enrollment::forSchool($this->schoolId)->count())->toBe(2)
        ->and($enrollment->fresh()->status)->toBe(EnrollmentStatus::Archived)
        ->and($enrollment->fresh()->deleted_at)->toBeNull();

    $next = Enrollment::forSchool($this->schoolId)->forYear($nextYear->id)->first();

    expect($next->status)->toBe(EnrollmentStatus::Active)
        ->and($next->enrollment_type)->toBe(EnrollmentType::Promotion);
});

test('archived enrollment rows are immutable', function (): void {
    $enrollment = app(CreateEnrollmentAction::class)->execute(historyEnrollmentData($this, $this->student));

    $nextYear = makeNextAcademicYear($this->schoolId, $this->year);

    $nextClass = SchoolClass::create([
        'school_id' => $this->schoolId,
        'academic_program_id' => $this->program->id,
        'education_level_id' => $this->level->id,
        'name' => '5ème A',
        'code' => 'CL5A',
        'status' => 'active',
    ]);

    app(PromoteStudentAction::class)->execute($enrollment, new PromotionData(
        decision: PromotionDecision::Promoted,
        academicCalendarYearId: $nextYear->id,
        promotedOn: '2027-09-15',
        toClassId: $nextClass->id,
    ));

    $archived = $enrollment->fresh();

    expect($archived->status)->toBe(EnrollmentStatus::Archived);

    app(UpdateEnrollmentAction::class)->execute($archived, new UpdateEnrollmentData(notes: 'Trop tard'));
})->throws(EnrollmentStateException::class);

test('graduation and withdrawal preserve the closed row', function (): void {
    $enrollment = app(CreateEnrollmentAction::class)->execute(historyEnrollmentData($this, $this->student));

    app(UpdateEnrollmentAction::class)->execute($enrollment, new UpdateEnrollmentData(notes: 'Note'));

    expect(Enrollment::forSchool($this->schoolId)->count())->toBe(1);

    $actions = collect(['suspended', 'withdrawn']);
    $actions->each(function (string $action) use (&$enrollment): void {
        $service = app(\App\Domains\Enrollment\Application\Services\EnrollmentService::class);

        $enrollment = $action === 'suspended'
            ? $service->suspend($enrollment, 'Retard')
            : $service->withdraw($enrollment, 'Déménagement');
    });

    expect($enrollment->fresh()->status)->toBe(EnrollmentStatus::Withdrawn)
        ->and(Enrollment::forSchool($this->schoolId)->count())->toBe(1);
});

test('history mirrors the full lifecycle in order', function (): void {
    $enrollment = app(CreateEnrollmentAction::class)->execute(historyEnrollmentData($this, $this->student));

    app(UpdateEnrollmentAction::class)->execute($enrollment, new UpdateEnrollmentData(notes: 'Notes'));

    $service = app(\App\Domains\Enrollment\Application\Services\EnrollmentService::class);
    $service->suspend($enrollment->fresh(), 'Retard');
    $service->restore($enrollment->fresh());

    expect(EnrollmentHistory::where('enrollment_id', $enrollment->id)->orderBy('id')->pluck('event')->all())
        ->toBe(['created', 'updated', 'suspended', 'restored']);
});

test('history rows are append-only', function (): void {
    $enrollment = app(CreateEnrollmentAction::class)->execute(historyEnrollmentData($this, $this->student));

    app(UpdateEnrollmentAction::class)->execute($enrollment->fresh(), new UpdateEnrollmentData(notes: 'a'));
    app(UpdateEnrollmentAction::class)->execute($enrollment->fresh(), new UpdateEnrollmentData(notes: 'b'));

    expect(EnrollmentHistory::where('enrollment_id', $enrollment->id)->count())->toBe(3);
});
