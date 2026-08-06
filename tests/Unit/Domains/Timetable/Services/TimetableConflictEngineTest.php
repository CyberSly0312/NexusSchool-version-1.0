<?php

declare(strict_types=1);

use App\Domains\Timetable\Application\Services\TimetableConflictEngine;
use App\Domains\Timetable\Domain\Enums\ConflictType;
use App\Domains\Timetable\Infrastructure\Models\AvailabilityConstraint;
use App\Domains\Timetable\Infrastructure\Models\ScheduleEntry;
use App\Domains\Timetable\Infrastructure\Models\Timetable;

function engineEntry(array $attributes): ScheduleEntry
{
    $entry = new ScheduleEntry(array_merge([
        'school_id' => 'sch_1',
        'timetable_id' => 1,
        'academic_subject_id' => 1,
        'school_class_id' => 1,
        'day_of_week' => 0,
        'period_number' => 1,
        'starts_at' => '08:00',
        'ends_at' => '08:55',
        'entry_type' => 'lesson',
        'status' => 'scheduled',
    ], $attributes));

    if (isset($attributes['id'])) {
        $entry->setAttribute('id', $attributes['id']);
    }

    return $entry;
}

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    makeTeachingGrid($this->schoolId);
    $this->timetable = Timetable::factory()->forSchool($this->schoolId)->create();
    $this->engine = app(TimetableConflictEngine::class);
});

test('a teacher double-booking produces teacher conflicts', function (): void {
    $entries = [
        engineEntry(['id' => 1, 'staff_id' => 9, 'school_class_id' => 1]),
        engineEntry(['id' => 2, 'staff_id' => 9, 'school_class_id' => 2]),
    ];

    $reports = $this->engine->detect($this->timetable, $entries, collect());

    expect($reports)->toHaveCount(1)
        ->and($reports[0]->type)->toBe(ConflictType::TeacherConflict)
        ->and($reports[0]->conflictingEntryId)->toBe(2);
});

test('the same class twice in the same period is a duplicate period', function (): void {
    $entries = [
        engineEntry(['id' => 1, 'staff_id' => 9, 'school_class_id' => 5]),
        engineEntry(['id' => 2, 'staff_id' => 10, 'school_class_id' => 5]),
    ];

    $reports = $this->engine->detect($this->timetable, $entries, collect());

    expect($reports[0]->type)->toBe(ConflictType::DuplicatePeriod);
});

test('overlapping periods for the same class are a class conflict', function (): void {
    $entries = [
        engineEntry(['id' => 1, 'school_class_id' => 5, 'period_number' => 1, 'starts_at' => '08:00', 'ends_at' => '10:00']),
        engineEntry(['id' => 2, 'school_class_id' => 5, 'period_number' => 2, 'starts_at' => '09:00', 'ends_at' => '11:00']),
    ];

    $reports = $this->engine->detect($this->timetable, $entries, collect());

    expect($reports[0]->type)->toBe(ConflictType::ClassConflict);
});

test('a section and a room double-booking are both reported', function (): void {
    $entries = [
        engineEntry(['id' => 1, 'class_section_id' => 3, 'room_id' => 'A-101', 'school_class_id' => 1]),
        engineEntry(['id' => 2, 'class_section_id' => 3, 'room_id' => 'A-101', 'school_class_id' => 2]),
    ];

    $reports = $this->engine->detect($this->timetable, $entries, collect());

    expect(collect($reports)->map(fn ($r) => $r->type)->all())
        ->toContain(ConflictType::SectionConflict)
        ->toContain(ConflictType::RoomConflict);
});

test('an availability constraint on a teacher is reported', function (): void {
    $entries = [engineEntry(['id' => 1, 'staff_id' => 9])];

    $constraints = collect([
        new AvailabilityConstraint([
            'constraint_type' => 'teacher',
            'target_id' => '9',
            'day_of_week' => 0,
            'starts_at' => '08:00',
            'ends_at' => '12:00',
        ]),
    ]);

    $reports = $this->engine->detect($this->timetable, $entries, $constraints);

    expect(collect($reports)->map(fn ($r) => $r->type)->all())
        ->toContain(ConflictType::UnavailableTeacher);
});

test('entries outside the teaching grid are flagged as unavailable period', function (): void {
    $entries = [engineEntry(['id' => 1, 'starts_at' => '07:00', 'ends_at' => '07:55'])];

    $reports = $this->engine->detect($this->timetable, $entries, collect());

    expect(collect($reports)->map(fn ($r) => $r->type)->all())
        ->toContain(ConflictType::UnavailablePeriod);
});

test('cancelled entries are ignored by the engine', function (): void {
    $entries = [
        engineEntry(['id' => 1, 'staff_id' => 9, 'school_class_id' => 1]),
        engineEntry(['id' => 2, 'staff_id' => 9, 'school_class_id' => 2, 'status' => 'cancelled']),
    ];

    $reports = $this->engine->detect($this->timetable, $entries, collect());

    expect($reports)->toHaveCount(0);
});

test('entries on different days never conflict', function (): void {
    $entries = [
        engineEntry(['id' => 1, 'staff_id' => 9, 'school_class_id' => 1, 'day_of_week' => 0]),
        engineEntry(['id' => 2, 'staff_id' => 9, 'school_class_id' => 1, 'day_of_week' => 1]),
    ];

    $reports = $this->engine->detect($this->timetable, $entries, collect());

    expect($reports)->toHaveCount(0);
});
