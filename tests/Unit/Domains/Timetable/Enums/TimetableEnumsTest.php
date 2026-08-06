<?php

declare(strict_types=1);

use App\Domains\Timetable\Domain\Enums\AvailabilityConstraintType;
use App\Domains\Timetable\Domain\Enums\ConflictType;
use App\Domains\Timetable\Domain\Enums\RecurrenceFrequency;
use App\Domains\Timetable\Domain\Enums\RoomAssignmentPurpose;
use App\Domains\Timetable\Domain\Enums\ScheduleEntryStatus;
use App\Domains\Timetable\Domain\Enums\ScheduleEntryType;
use App\Domains\Timetable\Domain\Enums\TimetableStatus;

test('timetable statuses map to their database values', function (): void {
    expect(TimetableStatus::Draft->value)->toBe('draft')
        ->and(TimetableStatus::Published->value)->toBe('published')
        ->and(TimetableStatus::Archived->value)->toBe('archived')
        ->and(TimetableStatus::Draft->label())->toBe('Brouillon')
        ->and(TimetableStatus::Published->color())->toBe('success');
});

test('schedule entry statuses round trip from the database', function (): void {
    foreach (ScheduleEntryStatus::cases() as $status) {
        expect(ScheduleEntryStatus::from($status->value))->toBe($status);
    }
});

test('schedule entry types cover lesson, assessment, activity and other', function (): void {
    expect(ScheduleEntryType::Lesson->value)->toBe('lesson')
        ->and(ScheduleEntryType::Assessment->value)->toBe('assessment')
        ->and(ScheduleEntryType::Activity->value)->toBe('activity')
        ->and(ScheduleEntryType::Other->value)->toBe('other');
});

test('conflict types are exhaustive and labeled in French', function (): void {
    expect(ConflictType::cases())->toHaveCount(8)
        ->and(ConflictType::TeacherConflict->label())->toBe('Conflit enseignant')
        ->and(ConflictType::RoomConflict->color())->toBe('danger');
});

test('availability constraints cover the three schedulable resources', function (): void {
    expect(AvailabilityConstraintType::Teacher->value)->toBe('teacher')
        ->and(AvailabilityConstraintType::Room->value)->toBe('room')
        ->and(AvailabilityConstraintType::ClassGroup->value)->toBe('class');
});

test('recurrence frequencies are weekly, biweekly and monthly', function (): void {
    expect(RecurrenceFrequency::Weekly->value)->toBe('weekly')
        ->and(RecurrenceFrequency::BiWeekly->value)->toBe('biweekly')
        ->and(RecurrenceFrequency::Monthly->value)->toBe('monthly');
});

test('room assignment purposes are lesson, assessment, event and other', function (): void {
    expect(RoomAssignmentPurpose::Lesson->value)->toBe('lesson')
        ->and(RoomAssignmentPurpose::Assessment->value)->toBe('assessment')
        ->and(RoomAssignmentPurpose::Event->value)->toBe('event')
        ->and(RoomAssignmentPurpose::Other->value)->toBe('other');
});
