<?php

declare(strict_types=1);

use App\Domains\Attendance\Application\Services\AttendanceRiskEngine;
use App\Domains\Attendance\Application\Services\AttendanceStatisticsCalculator;
use App\Domains\Attendance\Domain\Enums\AttendanceStatus;
use App\Domains\Attendance\Domain\ValueObjects\AttendancePercentage;

function attendanceRows(array $statuses): array
{
    return array_map(
        fn (AttendanceStatus $status, int $index): array => [
            'date' => attendanceMonday($index + 1),
            'status' => $status,
        ],
        $statuses,
        array_keys($statuses),
    );
}

test('percentage counts present-equivalent over the non-neutral denominator', function (): void {
    $calculator = app(AttendanceStatisticsCalculator::class);

    $rows = attendanceRows([
        AttendanceStatus::Present,
        AttendanceStatus::Late,
        AttendanceStatus::Absent,
        AttendanceStatus::Excused,
        AttendanceStatus::Remote,
    ]);

    $percentage = $calculator->percentage($rows);

    expect($percentage->value)->toBe(75.0)
        ->and($percentage->presentCount)->toBe(3)
        ->and($percentage->denominator)->toBe(4);
});

test('percentage of an empty history is zero', function (): void {
    $percentage = app(AttendanceStatisticsCalculator::class)->percentage([]);

    expect($percentage->value)->toBe(0.0)
        ->and($percentage->denominator)->toBe(0);
});

test('late percentage is the share of lates among present-equivalents', function (): void {
    $calculator = app(AttendanceStatisticsCalculator::class);

    $rows = attendanceRows([
        AttendanceStatus::Present,
        AttendanceStatus::Late,
        AttendanceStatus::Late,
        AttendanceStatus::Absent,
        AttendanceStatus::Excused,
    ]);

    expect($calculator->latePercentage($rows))->toBe(66.67);
});

test('late percentage is zero without any present-equivalent record', function (): void {
    $rows = attendanceRows([AttendanceStatus::Absent, AttendanceStatus::Excused]);

    expect(app(AttendanceStatisticsCalculator::class)->latePercentage($rows))->toBe(0.0);
});

test('consecutive absences counts the trailing absent streak and stops at a present', function (): void {
    $calculator = app(AttendanceStatisticsCalculator::class);

    $rows = attendanceRows([
        AttendanceStatus::Present,
        AttendanceStatus::Absent,
        AttendanceStatus::Absent,
        AttendanceStatus::Absent,
    ]);

    expect($calculator->consecutiveAbsences($rows))->toBe(3);
});

test('consecutive absences ignores trailing neutral rows', function (): void {
    $rows = attendanceRows([
        AttendanceStatus::Absent,
        AttendanceStatus::Absent,
        AttendanceStatus::Holiday,
        AttendanceStatus::Excused,
    ]);

    expect(app(AttendanceStatisticsCalculator::class)->consecutiveAbsences($rows))->toBe(2);
});

test('consecutive absences resets once a present-equivalent appears', function (): void {
    $rows = attendanceRows([
        AttendanceStatus::Absent,
        AttendanceStatus::Present,
        AttendanceStatus::Absent,
    ]);

    expect(app(AttendanceStatisticsCalculator::class)->consecutiveAbsences($rows))->toBe(1);
});

test('consecutive lates counts the trailing late streak', function (): void {
    $calculator = app(AttendanceStatisticsCalculator::class);

    $rows = attendanceRows([
        AttendanceStatus::Present,
        AttendanceStatus::Late,
        AttendanceStatus::Late,
        AttendanceStatus::Late,
    ]);

    expect($calculator->consecutiveLates($rows))->toBe(3);
});

test('trend is current minus previous percentage in points', function (): void {
    $calculator = app(AttendanceStatisticsCalculator::class);

    $current = attendanceRows([
        AttendanceStatus::Present,
        AttendanceStatus::Present,
        AttendanceStatus::Present,
        AttendanceStatus::Present,
    ]);
    $previous = attendanceRows([
        AttendanceStatus::Present,
        AttendanceStatus::Present,
        AttendanceStatus::Absent,
        AttendanceStatus::Absent,
    ]);

    expect($calculator->trend($current, $previous))->toBe(50.0);
});

test('risk engine combines the four weighted factors into a 0-100 score', function (): void {
    $engine = new AttendanceRiskEngine();

    $perfect = $engine->score(
        AttendancePercentage::fromFloat(100.0),
        0.0,
        0,
        0,
    );
    $chronic = $engine->score(
        AttendancePercentage::fromFloat(0.0),
        100.0,
        10,
        10,
    );

    expect($perfect->value)->toBe(0.0)
        ->and($chronic->value)->toBe(100.0)
        ->and($perfect->level())->toBe('low')
        ->and($chronic->level())->toBe('high');
});

test('risk engine clamps consecutive streaks at ten days for normalization', function (): void {
    $engine = new AttendanceRiskEngine();

    $score = $engine->score(
        AttendancePercentage::fromFloat(100.0),
        0.0,
        30,
        0,
    );

    expect($score->value)->toBe(20.0);
});

test('risk engine honours the configured weights', function (): void {
    $engine = new AttendanceRiskEngine([
        'attendance_percentage' => 1.0,
        'late_percentage' => 0.0,
        'consecutive_absences' => 0.0,
        'consecutive_lates' => 0.0,
    ]);

    $score = $engine->score(
        AttendancePercentage::fromFloat(50.0),
        0.0,
        0,
        0,
    );

    expect($score->value)->toBe(50.0);
});

test('risk engine rejects a score outside [0, 100]', function (): void {
    app(AttendanceStatisticsCalculator::class)->percentage(
        attendanceRows([AttendanceStatus::Present]),
    );
    new AttendanceRiskEngine();
    expect(AttendancePercentage::fromFloat(-5.0)->value)->toBe(0.0)
        ->and(AttendancePercentage::fromFloat(105.0)->value)->toBe(100.0);
});
