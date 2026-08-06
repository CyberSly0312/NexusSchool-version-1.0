<?php

declare(strict_types=1);

use App\Domains\Timetable\Domain\ValueObjects\AcademicWeek;
use App\Domains\Timetable\Domain\ValueObjects\DayOfWeek;
use App\Domains\Timetable\Domain\ValueObjects\LessonDuration;
use App\Domains\Timetable\Domain\ValueObjects\PeriodNumber;
use App\Domains\Timetable\Domain\ValueObjects\RoomCode;
use App\Domains\Timetable\Domain\ValueObjects\TimeRange;

test('day of week follows the Monday-first convention', function (): void {
    expect(DayOfWeek::from(0)->value)->toBe(0)
        ->and(DayOfWeek::from(0)->label())->toBe('Lundi')
        ->and(DayOfWeek::from(4)->label())->toBe('Vendredi')
        ->and(DayOfWeek::from(0)->shortLabel())->toBe('Lun');
});

test('day of week converts a Carbon day into the teaching convention', function (): void {
    expect(DayOfWeek::fromCarbonDay(0)->value)->toBe(6)   // Carbon Sunday
        ->and(DayOfWeek::fromCarbonDay(1)->value)->toBe(0); // Carbon Monday
});

test('day of week rejects out of range values', function (int $day): void {
    DayOfWeek::from($day);
})->with([-1, 7, 12])->throws(InvalidArgumentException::class);

test('time range computes its duration in minutes', function (): void {
    $range = TimeRange::from('08:00', '09:55');

    expect($range->durationMinutes())->toBe(115);
});

test('time range rejects empty or inverted ranges', function (string $start, string $end): void {
    TimeRange::from($start, $end);
})->with([
    'equal' => ['08:00', '08:00'],
    'inverted' => ['10:00', '09:00'],
])->throws(InvalidArgumentException::class);

test('time range detects overlaps including partial overlaps', function (): void {
    expect(TimeRange::from('08:00', '10:00')->overlaps(TimeRange::from('09:00', '11:00')))->toBeTrue()
        ->and(TimeRange::from('08:00', '10:00')->overlaps(TimeRange::from('10:00', '12:00')))->toBeFalse()
        ->and(TimeRange::from('08:00', '10:00')->overlaps(TimeRange::from('11:00', '12:00')))->toBeFalse();
});

test('time range contains boundaries', function (): void {
    $range = TimeRange::from('08:00', '10:00');

    expect($range->contains('08:00'))->toBeTrue()
        ->and($range->contains('10:00'))->toBeTrue()
        ->and($range->contains('09:30'))->toBeTrue()
        ->and($range->contains('10:01'))->toBeFalse();
});

test('time range rejects malformed times', function (): void {
    TimeRange::from('08h00', '09:00');
})->throws(InvalidArgumentException::class);

test('lesson duration accepts any positive length', function (): void {
    expect(LessonDuration::from(55)->minutes)->toBe(55)
        ->and(LessonDuration::from(60)->toHours())->toBe('1.00');
});

test('lesson duration rejects zero or negative lengths', function (): void {
    LessonDuration::from(0);
})->throws(InvalidArgumentException::class);

test('academic week spans monday to sunday', function (): void {
    $week = AcademicWeek::from('2026-09-07');

    expect($week->weekEnd())->toBe('2026-09-13')
        ->and($week->contains('2026-09-07'))->toBeTrue()
        ->and($week->contains('2026-09-13'))->toBeTrue()
        ->and($week->contains('2026-09-14'))->toBeFalse()
        ->and($week->label())->toBe('Semaine du 07/09/2026 au 13/09/2026');
});

test('academic week rejects non monday starts', function (): void {
    AcademicWeek::from('2026-09-08');
})->throws(InvalidArgumentException::class);

test('academic week rejects malformed dates', function (): void {
    AcademicWeek::from('07/09/2026');
})->throws(InvalidArgumentException::class);

test('period number is 1-based', function (): void {
    expect(PeriodNumber::from(1)->value)->toBe(1)
        ->and(PeriodNumber::from(8)->label())->toBe('P8');
});

test('period number rejects zero', function (): void {
    PeriodNumber::from(0);
})->throws(InvalidArgumentException::class);

test('room code is canonicalized to uppercase', function (): void {
    expect(RoomCode::from('salle-a')->value)->toBe('SALLE-A')
        ->and((string) RoomCode::from('b104'))->toBe('B104');
});

test('room code rejects unsupported characters', function (): void {
    RoomCode::from('salle_1');
})->throws(InvalidArgumentException::class);

test('room code exposes a static validator', function (): void {
    expect(RoomCode::isSupported('S-101'))->toBeTrue()
        ->and(RoomCode::isSupported('salle 1'))->toBeFalse();
});
