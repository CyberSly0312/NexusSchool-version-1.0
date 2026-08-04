<?php

declare(strict_types=1);

use App\Domains\Academic\Infrastructure\Models\ClassSection;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\Enrollment\Application\Actions\CreateEnrollmentAction;
use App\Domains\Enrollment\Application\Actions\GraduateStudentAction;
use App\Domains\Enrollment\Application\Actions\PromoteStudentAction;
use App\Domains\Enrollment\Application\Actions\RepeatStudentAction;
use App\Domains\Enrollment\Application\Actions\RestoreEnrollmentAction;
use App\Domains\Enrollment\Application\Actions\SuspendEnrollmentAction;
use App\Domains\Enrollment\Application\Actions\TransferEnrollmentAction;
use App\Domains\Enrollment\Application\Actions\UpdateEnrollmentAction;
use App\Domains\Enrollment\Application\Actions\WithdrawStudentAction;
use App\Domains\Enrollment\Application\DTOs\CreateEnrollmentData;
use App\Domains\Enrollment\Application\DTOs\GraduationData;
use App\Domains\Enrollment\Application\DTOs\PromotionData;
use App\Domains\Enrollment\Application\DTOs\TransferData;
use App\Domains\Enrollment\Application\DTOs\UpdateEnrollmentData;
use App\Domains\Enrollment\Domain\Enums\EnrollmentStatus;
use App\Domains\Enrollment\Domain\Enums\EnrollmentType;
use App\Domains\Enrollment\Domain\Enums\GraduationDecision;
use App\Domains\Enrollment\Domain\Enums\PromotionDecision;
use App\Domains\Enrollment\Domain\Enums\TransferType;
use App\Domains\Enrollment\Domain\Events\EnrollmentRestored;
use App\Domains\Enrollment\Domain\Events\EnrollmentSuspended;
use App\Domains\Enrollment\Domain\Events\StudentGraduated;
use App\Domains\Enrollment\Domain\Events\StudentPromoted;
use App\Domains\Enrollment\Domain\Events\StudentRepeated;
use App\Domains\Enrollment\Domain\Events\StudentTransferred;
use App\Domains\Enrollment\Domain\Events\StudentWithdrawn;
use App\Domains\Enrollment\Domain\Exceptions\EnrollmentStateException;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\Enrollment\Infrastructure\Models\EnrollmentGraduation;
use App\Domains\Enrollment\Infrastructure\Models\EnrollmentPromotion;
use App\Domains\Enrollment\Infrastructure\Models\EnrollmentTransfer;
use App\Domains\Student\Infrastructure\Models\Student;
use Illuminate\Support\Facades\Event;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    [$this->year, $this->class, $this->program, $this->level] = makeEnrollmentContext($this->schoolId);
    $this->student = Student::factory()->create(['school_id' => $this->schoolId]);

    $this->enrollment = app(CreateEnrollmentAction::class)->execute(
        CreateEnrollmentData::fromArray([
            'school_id' => $this->schoolId,
            'student_id' => $this->student->id,
            'academic_calendar_year_id' => $this->year->id,
            'academic_program_id' => $this->program->id,
            'education_level_id' => $this->level->id,
            'school_class_id' => $this->class->id,
            'enrolled_at' => '2026-09-15',
        ]),
    );

    $this->nextYear = makeNextAcademicYear($this->schoolId, $this->year);

    $this->nextClass = SchoolClass::create([
        'school_id' => $this->schoolId,
        'academic_program_id' => $this->program->id,
        'education_level_id' => $this->level->id,
        'name' => '5ème A',
        'code' => 'CL5A',
        'status' => 'active',
    ]);
});

function promotionData(object $context): PromotionData
{
    return new PromotionData(
        decision: PromotionDecision::Promoted,
        academicCalendarYearId: $context->nextYear->id,
        promotedOn: '2027-09-15',
        toClassId: $context->nextClass->id,
    );
}

test('promotion archives the source and creates a new enrollment row', function (): void {
    Event::fake([StudentPromoted::class]);

    $result = app(PromoteStudentAction::class)->execute($this->enrollment, promotionData($this));

    expect($result->fresh()->status)->toBe(EnrollmentStatus::Archived)
        ->and($result->fresh()->exited_at->toDateString())->toBe('2027-09-15');

    $promotion = EnrollmentPromotion::where('enrollment_id', $this->enrollment->id)->first();

    expect($promotion)->not->toBeNull()
        ->and($promotion->decision)->toBe(PromotionDecision::Promoted)
        ->and($promotion->from_class_id)->toBe($this->class->id)
        ->and($promotion->to_class_id)->toBe($this->nextClass->id);

    $next = Enrollment::forSchool($this->schoolId)
        ->where('student_id', $this->student->id)
        ->forYear($this->nextYear->id)
        ->first();

    expect($next)->not->toBeNull()
        ->and($next->enrollment_type)->toBe(EnrollmentType::Promotion)
        ->and($next->school_class_id)->toBe($this->nextClass->id)
        ->and($next->status)->toBe(EnrollmentStatus::Active);

    Event::assertDispatched(StudentPromoted::class);
});

test('promotion is rejected from a non-active enrollment', function (): void {
    app(WithdrawStudentAction::class)->execute($this->enrollment);

    app(PromoteStudentAction::class)->execute(
        $this->enrollment->fresh(),
        new PromotionData(
            decision: PromotionDecision::Promoted,
            academicCalendarYearId: $this->nextYear->id,
            promotedOn: '2027-09-15',
            toClassId: $this->nextClass->id,
        ),
    );
})->throws(EnrollmentStateException::class);

test('promotion requires a target class', function (): void {
    app(PromoteStudentAction::class)->execute(
        $this->enrollment,
        new PromotionData(
            decision: PromotionDecision::Promoted,
            academicCalendarYearId: $this->nextYear->id,
            promotedOn: '2027-09-15',
            toClassId: null,
        ),
    );
})->throws(InvalidArgumentException::class);

test('repetition archives the source and re-enrolls in the same class', function (): void {
    Event::fake([StudentRepeated::class]);

    app(RepeatStudentAction::class)->execute(
        $this->enrollment,
        new PromotionData(
            decision: PromotionDecision::Repeated,
            academicCalendarYearId: $this->nextYear->id,
            promotedOn: '2027-09-15',
        ),
    );

    expect($this->enrollment->fresh()->status)->toBe(EnrollmentStatus::Archived);

    $next = Enrollment::forSchool($this->schoolId)
        ->where('student_id', $this->student->id)
        ->forYear($this->nextYear->id)
        ->first();

    expect($next)->not->toBeNull()
        ->and($next->enrollment_type)->toBe(EnrollmentType::Repetition)
        ->and($next->school_class_id)->toBe($this->class->id);

    Event::assertDispatched(StudentRepeated::class);
});

test('repeat flow requires the repeated decision', function (): void {
    app(RepeatStudentAction::class)->execute($this->enrollment, promotionData($this));
})->throws(InvalidArgumentException::class);

test('an internal transfer keeps the enrollment active and updates its class', function (): void {
    Event::fake([StudentTransferred::class]);

    $section = ClassSection::create([
        'school_id' => $this->schoolId,
        'school_class_id' => $this->nextClass->id,
        'name' => 'A',
        'code' => 'SEC-A',
        'status' => 'active',
    ]);

    $result = app(TransferEnrollmentAction::class)->execute(
        $this->enrollment,
        new TransferData(
            transferType: TransferType::Internal,
            transferDate: '2026-11-15',
            toClassId: $this->nextClass->id,
            toSectionId: $section->id,
        ),
    );

    $fresh = $result->fresh();

    expect($fresh->status)->toBe(EnrollmentStatus::Active)
        ->and($fresh->school_class_id)->toBe($this->nextClass->id)
        ->and($fresh->class_section_id)->toBe($section->id)
        ->and($fresh->academic_program_id)->toBe($this->program->id)
        ->and($fresh->education_level_id)->toBe($this->level->id);

    $transfer = EnrollmentTransfer::where('enrollment_id', $this->enrollment->id)->first();

    expect($transfer)->not->toBeNull()
        ->and($transfer->transfer_type)->toBe(TransferType::Internal)
        ->and($transfer->from_class_id)->toBe($this->class->id)
        ->and($transfer->to_class_id)->toBe($this->nextClass->id)
        ->and($transfer->from_section_id)->toBeNull()
        ->and($transfer->to_section_id)->toBe($section->id);

    Event::assertDispatched(StudentTransferred::class);
});

test('an outgoing school transfer archives the enrollment', function (): void {
    $result = app(TransferEnrollmentAction::class)->execute(
        $this->enrollment,
        new TransferData(
            transferType: TransferType::SchoolTransfer,
            transferDate: '2026-11-15',
            toSchoolId: 'SCH-OUT-1',
            reason: 'Déménagement',
        ),
    );

    expect($result->fresh()->status)->toBe(EnrollmentStatus::Archived)
        ->and($result->fresh()->exited_at->toDateString())->toBe('2026-11-15');

    $transfer = EnrollmentTransfer::where('enrollment_id', $this->enrollment->id)->first();

    expect($transfer->transfer_type)->toBe(TransferType::SchoolTransfer)
        ->and($transfer->to_school_id)->toBe('SCH-OUT-1');
});

test('an internal transfer requires a target class or section', function (): void {
    app(TransferEnrollmentAction::class)->execute(
        $this->enrollment,
        new TransferData(
            transferType: TransferType::Internal,
            transferDate: '2026-11-15',
        ),
    );
})->throws(InvalidArgumentException::class);

test('a transfer is rejected from an archived enrollment', function (): void {
    app(TransferEnrollmentAction::class)->execute(
        $this->enrollment,
        new TransferData(
            transferType: TransferType::SchoolTransfer,
            transferDate: '2026-11-15',
            toSchoolId: 'SCH-OUT-1',
        ),
    );

    app(TransferEnrollmentAction::class)->execute(
        $this->enrollment->fresh(),
        new TransferData(
            transferType: TransferType::SchoolTransfer,
            transferDate: '2026-11-20',
            toSchoolId: 'SCH-OUT-2',
        ),
    );
})->throws(EnrollmentStateException::class);

test('graduation closes the enrollment and records the diploma', function (): void {
    Event::fake([StudentGraduated::class]);

    $result = app(GraduateStudentAction::class)->execute(
        $this->enrollment,
        new GraduationData(
            decision: GraduationDecision::Graduated,
            educationLevelId: $this->level->id,
            graduatedOn: '2027-07-01',
            certificateReference: 'CERT-2027-001',
        ),
    );

    expect($result->fresh()->status)->toBe(EnrollmentStatus::Graduated)
        ->and($result->fresh()->exited_at->toDateString())->toBe('2027-07-01');

    $graduation = EnrollmentGraduation::where('enrollment_id', $this->enrollment->id)->first();

    expect($graduation)->not->toBeNull()
        ->and($graduation->decision)->toBe(GraduationDecision::Graduated)
        ->and($graduation->certificate_reference)->toBe('CERT-2027-001');

    Event::assertDispatched(StudentGraduated::class);
});

test('graduation is rejected from a non-active enrollment', function (): void {
    app(WithdrawStudentAction::class)->execute($this->enrollment);

    app(GraduateStudentAction::class)->execute(
        $this->enrollment->fresh(),
        new GraduationData(
            decision: GraduationDecision::Graduated,
            educationLevelId: $this->level->id,
            graduatedOn: '2027-07-01',
        ),
    );
})->throws(EnrollmentStateException::class);

test('withdrawal closes the enrollment with an exit reason', function (): void {
    Event::fake([StudentWithdrawn::class]);

    $result = app(WithdrawStudentAction::class)->execute($this->enrollment, 'Déménagement familial');

    expect($result->fresh()->status)->toBe(EnrollmentStatus::Withdrawn)
        ->and($result->fresh()->exit_reason)->toBe('Déménagement familial')
        ->and($result->fresh()->exited_at)->not->toBeNull();

    Event::assertDispatched(StudentWithdrawn::class);
});

test('suspension pauses an active enrollment', function (): void {
    Event::fake([EnrollmentSuspended::class]);

    $result = app(SuspendEnrollmentAction::class)->execute($this->enrollment, 'Retard répété', '2026-12-31');

    expect($result->fresh()->status)->toBe(EnrollmentStatus::Suspended)
        ->and($result->fresh()->suspension_reason)->toBe('Retard répété')
        ->and($result->fresh()->suspended_until->toDateString())->toBe('2026-12-31');

    Event::assertDispatched(EnrollmentSuspended::class);
});

test('restore reactivates a suspended enrollment', function (): void {
    Event::fake([EnrollmentRestored::class]);

    app(SuspendEnrollmentAction::class)->execute($this->enrollment, 'Retard répété');

    $result = app(RestoreEnrollmentAction::class)->execute($this->enrollment->fresh());

    expect($result->fresh()->status)->toBe(EnrollmentStatus::Active)
        ->and($result->fresh()->suspension_reason)->toBeNull()
        ->and($result->fresh()->suspended_until)->toBeNull();

    Event::assertDispatched(EnrollmentRestored::class);
});

test('suspend is rejected from a non-active enrollment', function (): void {
    app(WithdrawStudentAction::class)->execute($this->enrollment);

    app(SuspendEnrollmentAction::class)->execute($this->enrollment->fresh(), 'Retard');
})->throws(EnrollmentStateException::class);

test('restore is rejected from a non-suspended enrollment', function (): void {
    app(RestoreEnrollmentAction::class)->execute($this->enrollment);
})->throws(EnrollmentStateException::class);

test('update modifies the mutable metadata of an enrollment', function (): void {
    $section = ClassSection::create([
        'school_id' => $this->schoolId,
        'school_class_id' => $this->class->id,
        'name' => 'A',
        'code' => 'SEC-A',
        'status' => 'active',
    ]);

    $result = app(UpdateEnrollmentAction::class)->execute(
        $this->enrollment,
        new UpdateEnrollmentData(
            classSectionId: $section->id,
            notes: 'Inscription validée',
            enrolledAt: '2026-10-01',
        ),
    );

    expect($result->fresh()->class_section_id)->toBe($section->id)
        ->and($result->fresh()->notes)->toBe('Inscription validée')
        ->and($result->fresh()->enrolled_at->toDateString())->toBe('2026-10-01');
});

test('update rejects a section that does not belong to the enrollment class', function (): void {
    $foreignSection = ClassSection::create([
        'school_id' => $this->schoolId,
        'school_class_id' => $this->nextClass->id,
        'name' => 'B',
        'code' => 'SEC-B',
        'status' => 'active',
    ]);

    app(UpdateEnrollmentAction::class)->execute(
        $this->enrollment,
        new UpdateEnrollmentData(classSectionId: $foreignSection->id),
    );
})->throws(InvalidArgumentException::class);

test('update is rejected on an archived enrollment', function (): void {
    app(PromoteStudentAction::class)->execute($this->enrollment, promotionData($this));

    app(UpdateEnrollmentAction::class)->execute(
        $this->enrollment->fresh(),
        new UpdateEnrollmentData(notes: 'Trop tard'),
    );
})->throws(EnrollmentStateException::class);
