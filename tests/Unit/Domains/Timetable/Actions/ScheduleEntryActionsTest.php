<?php

declare(strict_types=1);

use App\Domains\Timetable\Application\Actions\AssignRoomAction;
use App\Domains\Timetable\Application\Actions\AssignTeacherAction;
use App\Domains\Timetable\Application\Actions\CreateScheduleEntryAction;
use App\Domains\Timetable\Application\Actions\UpdateScheduleEntryAction;
use App\Domains\Timetable\Application\DTOs\ScheduleEntryData;
use App\Domains\Timetable\Application\DTOs\UpdateScheduleEntryData;
use App\Domains\Timetable\Domain\Enums\ScheduleEntryStatus;
use App\Domains\Timetable\Domain\Exceptions\ContractViolationException;
use App\Domains\Timetable\Domain\Exceptions\InvalidTimetableStateException;
use App\Domains\Timetable\Domain\Exceptions\ScheduleEntryException;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->academic = makeTimetableAcademicContext($this->schoolId);
    $this->timetable = makeTimetable($this->schoolId, $this->academic['year']->id, $this->academic['term']->id);
    $this->teacher = makeTeacher($this->schoolId, [(int) $this->academic['subject']->id]);
});

test('a lesson can be scheduled on a draft timetable', function (): void {
    $entry = scheduleLesson(
        $this->schoolId,
        $this->timetable->id,
        $this->academic['subject']->id,
        $this->academic['class']->id,
        $this->teacher->id,
    );

    expect($entry->status)->toBe(ScheduleEntryStatus::Scheduled)
        ->and($entry->staff_id)->toBe($this->teacher->id)
        ->and($this->timetable->fresh()->total_entries)->toBe(1);
});

test('a subject or class from another school is rejected', function (): void {
    $foreign = makeTimetableAcademicContext(makeTenant());

    scheduleLesson(
        $this->schoolId,
        $this->timetable->id,
        $foreign['subject']->id,
        $this->academic['class']->id,
    );
})->throws(ContractViolationException::class);

test('a teacher who does not teach the subject is rejected', function (): void {
    $otherSubject = \App\Domains\Academic\Infrastructure\Models\AcademicSubject::create([
        'school_id' => $this->schoolId,
        'name' => 'Français',
        'code' => 'FRA',
        'abbreviation' => 'FRA',
        'status' => 'active',
    ]);
    $mathTeacher = makeTeacher($this->schoolId, [(int) $this->academic['subject']->id]);

    scheduleLesson(
        $this->schoolId,
        $this->timetable->id,
        $otherSubject->id,
        $this->academic['class']->id,
        $mathTeacher->id,
    );
})->throws(ContractViolationException::class);

test('a teacher cannot be double-booked on the same slot', function (): void {
    $otherClass = \App\Domains\Academic\Infrastructure\Models\SchoolClass::create([
        'school_id' => $this->schoolId,
        'academic_program_id' => $this->academic['program']->id,
        'name' => '6ème B',
        'code' => 'CL6B',
        'status' => 'active',
    ]);

    scheduleLesson($this->schoolId, $this->timetable->id, $this->academic['subject']->id, $this->academic['class']->id, $this->teacher->id);

    scheduleLesson($this->schoolId, $this->timetable->id, $this->academic['subject']->id, $otherClass->id, $this->teacher->id);
})->throws(ScheduleEntryException::class);

test('a class cannot be scheduled twice on the same slot', function (): void {
    $otherTeacher = makeTeacher($this->schoolId, [(int) $this->academic['subject']->id], 'Awa', 'Kaboré');

    scheduleLesson($this->schoolId, $this->timetable->id, $this->academic['subject']->id, $this->academic['class']->id, $this->teacher->id);

    scheduleLesson($this->schoolId, $this->timetable->id, $this->academic['subject']->id, $this->academic['class']->id, $otherTeacher->id);
})->throws(ScheduleEntryException::class);

test('entries cannot be added to a published timetable', function (): void {
    $published = \App\Domains\Timetable\Infrastructure\Models\Timetable::factory()
        ->forSchool($this->schoolId)
        ->published()
        ->create();

    scheduleLesson($this->schoolId, $published->id, $this->academic['subject']->id, $this->academic['class']->id);
})->throws(InvalidTimetableStateException::class);

test('a timetable of another school cannot be edited', function (): void {
    $foreignTimetable = makeTimetable(makeTenant());

    scheduleLesson($this->schoolId, $foreignTimetable->id, $this->academic['subject']->id, $this->academic['class']->id);
})->throws(InvalidTimetableStateException::class);

test('an unavailable teacher cannot be assigned through the assign action', function (): void {
    $entry = scheduleLesson($this->schoolId, $this->timetable->id, $this->academic['subject']->id, $this->academic['class']->id);

    $suspended = makeTeacher($this->schoolId, [(int) $this->academic['subject']->id], 'Sita', 'Traoré');
    $suspended->update(['status' => 'suspended']);

    app(AssignTeacherAction::class)->execute($entry->id, $suspended->id);
})->throws(ContractViolationException::class);

test('assigning a teacher to an entry is audited and recorded', function (): void {
    $entry = scheduleLesson($this->schoolId, $this->timetable->id, $this->academic['subject']->id, $this->academic['class']->id);

    $updated = app(AssignTeacherAction::class)->execute($entry->id, $this->teacher->id);

    expect($updated->staff_id)->toBe($this->teacher->id);
});

test('assigning the same teacher twice in one slot is blocked', function (): void {
    $entry = scheduleLesson($this->schoolId, $this->timetable->id, $this->academic['subject']->id, $this->academic['class']->id);
    $entry->update(['staff_id' => $this->teacher->id]);

    $otherClass = \App\Domains\Academic\Infrastructure\Models\SchoolClass::create([
        'school_id' => $this->schoolId,
        'academic_program_id' => $this->academic['program']->id,
        'name' => '5ème A',
        'code' => 'CL5A',
        'status' => 'active',
    ]);

    app(CreateScheduleEntryAction::class)->execute(ScheduleEntryData::fromArray([
        'school_id' => $this->schoolId,
        'timetable_id' => $this->timetable->id,
        'academic_subject_id' => $this->academic['subject']->id,
        'school_class_id' => $otherClass->id,
        'day_of_week' => 0,
        'period_number' => 1,
        'starts_at' => '08:00',
        'ends_at' => '08:55',
    ]));

    $otherEntry = \App\Domains\Timetable\Infrastructure\Models\ScheduleEntry::where('school_class_id', $otherClass->id)->firstOrFail();

    app(AssignTeacherAction::class)->execute($otherEntry->id, $this->teacher->id);
})->throws(ScheduleEntryException::class);

test('a room can be assigned to an entry and journaled', function (): void {
    $room = \App\Models\Classroom::factory()->forSchool($this->schoolId)->create();

    $entry = scheduleLesson($this->schoolId, $this->timetable->id, $this->academic['subject']->id, $this->academic['class']->id);

    $updated = app(AssignRoomAction::class)->execute($entry->id, (string) $room->getKey());

    expect($updated->room_id)->toBe((string) $room->getKey())
        ->and(\App\Domains\Timetable\Infrastructure\Models\RoomAssignment::forSchool($this->schoolId)->count())->toBe(1);
});

test('an inactive room cannot be assigned', function (): void {
    $room = \App\Models\Classroom::factory()->forSchool($this->schoolId)->archived()->create();

    $entry = scheduleLesson($this->schoolId, $this->timetable->id, $this->academic['subject']->id, $this->academic['class']->id);

    app(AssignRoomAction::class)->execute($entry->id, (string) $room->getKey());
})->throws(ContractViolationException::class);

test('an entry can be moved to another slot', function (): void {
    $entry = scheduleLesson($this->schoolId, $this->timetable->id, $this->academic['subject']->id, $this->academic['class']->id);

    $updated = app(UpdateScheduleEntryAction::class)->execute(UpdateScheduleEntryData::fromArray([
        'entry_id' => $entry->id,
        'day_of_week' => 1,
        'period_number' => 2,
        'starts_at' => '09:00',
        'ends_at' => '09:55',
    ]));

    expect($updated->day_of_week)->toBe(1)
        ->and($updated->period_number)->toBe(2)
        ->and($updated->starts_at)->toBe('09:00');
});

test('moving an entry onto a booked slot is blocked', function (): void {
    scheduleLesson($this->schoolId, $this->timetable->id, $this->academic['subject']->id, $this->academic['class']->id, $this->teacher->id);

    $otherClass = \App\Domains\Academic\Infrastructure\Models\SchoolClass::create([
        'school_id' => $this->schoolId,
        'academic_program_id' => $this->academic['program']->id,
        'name' => '5ème B',
        'code' => 'CL5B',
        'status' => 'active',
    ]);

    $second = scheduleLesson(
        $this->schoolId,
        $this->timetable->id,
        $this->academic['subject']->id,
        $otherClass->id,
        $this->teacher->id,
        day: 1,
    );

    app(UpdateScheduleEntryAction::class)->execute(UpdateScheduleEntryData::fromArray([
        'entry_id' => $second->id,
        'day_of_week' => 0,
        'starts_at' => '08:00',
        'ends_at' => '08:55',
    ]));
})->throws(ScheduleEntryException::class);
