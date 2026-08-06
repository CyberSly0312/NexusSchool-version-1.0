<?php

declare(strict_types=1);

use App\Domains\Attendance\Domain\Enums\AttendanceAlertStatus;
use App\Domains\Attendance\Domain\Enums\AttendanceSessionStatus;
use App\Domains\Attendance\Domain\Enums\AttendanceStatus;

test('attendance status exposes a French label and a color for every case', function (): void {
    expect(AttendanceStatus::cases())->toHaveCount(10);

    foreach (AttendanceStatus::cases() as $status) {
        expect($status->label())->toBeString()->not->toBeEmpty()
            ->and($status->color())->toBeString()->not->toBeEmpty();
    }
});

test('attendance status options map every canonical status to its French label', function (): void {
    $options = AttendanceStatus::options();

    expect($options)->toBeArray()
        ->toHaveCount(10)
        ->and(array_values($options))->toContain('Présent', 'En retard', 'Excusé', 'Absent');
});

test('statuses classify correctly against the default configuration', function (): void {
    expect(AttendanceStatus::Present->countsAsPresent())->toBeTrue()
        ->and(AttendanceStatus::Late->countsAsPresent())->toBeTrue()
        ->and(AttendanceStatus::Remote->countsAsPresent())->toBeTrue()
        ->and(AttendanceStatus::SchoolActivity->countsAsPresent())->toBeTrue();

    expect(AttendanceStatus::Absent->countsAsAbsent())->toBeTrue()
        ->and(AttendanceStatus::Unexcused->countsAsAbsent())->toBeTrue();

    expect(AttendanceStatus::Excused->isNeutral())->toBeTrue()
        ->and(AttendanceStatus::Medical->isNeutral())->toBeTrue()
        ->and(AttendanceStatus::AuthorizedLeave->isNeutral())->toBeTrue()
        ->and(AttendanceStatus::Holiday->isNeutral())->toBeTrue();
});

test('session status exposes a French label and a color for every case', function (): void {
    expect(AttendanceSessionStatus::cases())->toHaveCount(6);

    foreach (AttendanceSessionStatus::cases() as $status) {
        expect($status->label())->toBeString()->not->toBeEmpty()
            ->and($status->color())->toBeString()->not->toBeEmpty();
    }
});

test('only open sessions are recordable', function (): void {
    expect(AttendanceSessionStatus::Open->isRecordable())->toBeTrue()
        ->and(AttendanceSessionStatus::TakingAttendance->isRecordable())->toBeTrue()
        ->and(AttendanceSessionStatus::Draft->isRecordable())->toBeFalse()
        ->and(AttendanceSessionStatus::Submitted->isRecordable())->toBeFalse()
        ->and(AttendanceSessionStatus::Locked->isRecordable())->toBeFalse()
        ->and(AttendanceSessionStatus::Archived->isRecordable())->toBeFalse();
});

test('locked and archived sessions are immutable', function (): void {
    expect(AttendanceSessionStatus::Locked->isImmutable())->toBeTrue()
        ->and(AttendanceSessionStatus::Archived->isImmutable())->toBeTrue()
        ->and(AttendanceSessionStatus::Draft->isImmutable())->toBeFalse()
        ->and(AttendanceSessionStatus::Open->isImmutable())->toBeFalse();
});

test('session lifecycle allows only the canonical forward transitions', function (): void {
    $transitions = [
        [AttendanceSessionStatus::Draft, AttendanceSessionStatus::Open, true],
        [AttendanceSessionStatus::Open, AttendanceSessionStatus::TakingAttendance, true],
        [AttendanceSessionStatus::TakingAttendance, AttendanceSessionStatus::Submitted, true],
        [AttendanceSessionStatus::TakingAttendance, AttendanceSessionStatus::Locked, true],
        [AttendanceSessionStatus::Submitted, AttendanceSessionStatus::Locked, true],
        [AttendanceSessionStatus::Locked, AttendanceSessionStatus::Archived, true],
        [AttendanceSessionStatus::Submitted, AttendanceSessionStatus::Archived, true],
        [AttendanceSessionStatus::Draft, AttendanceSessionStatus::Submitted, false],
        [AttendanceSessionStatus::Open, AttendanceSessionStatus::Draft, false],
        [AttendanceSessionStatus::Locked, AttendanceSessionStatus::Open, false],
        [AttendanceSessionStatus::Draft, AttendanceSessionStatus::Draft, false],
    ];

    foreach ($transitions as [$from, $to, $expected]) {
        expect($to->allowsTransitionFrom($from))->toBe($expected);
    }
});

test('alert status exposes a French label and a color for every case', function (): void {
    expect(AttendanceAlertStatus::cases())->toHaveCount(3);

    foreach (AttendanceAlertStatus::cases() as $status) {
        expect($status->label())->toBeString()->not->toBeEmpty()
            ->and($status->color())->toBeString()->not->toBeEmpty();
    }
});
