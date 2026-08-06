<?php

declare(strict_types=1);

use App\Domains\Attendance\Domain\Enums\AttendanceStatus;
use App\Domains\Attendance\Domain\ValueObjects\AttendancePercentage;
use App\Domains\Attendance\Domain\ValueObjects\AttendancePeriod;
use App\Domains\Attendance\Domain\ValueObjects\AttendanceStatusValue;
use App\Domains\Attendance\Domain\ValueObjects\CorrectionReason;
use App\Domains\Attendance\Domain\ValueObjects\RiskScore;
use App\Domains\Attendance\Domain\ValueObjects\TimeStamp;
use Carbon\CarbonImmutable;

test('attendance percentage computes counts and rounds to two decimals', function (): void {
    $percentage = AttendancePercentage::fromCounts(18, 20);

    expect($percentage->value)->toBe(90.0)
        ->and($percentage->presentCount)->toBe(18)
        ->and($percentage->denominator)->toBe(20)
        ->and($percentage->ratio())->toBe(0.9);
});

test('attendance percentage with an empty denominator is zero', function (): void {
    $percentage = AttendancePercentage::fromCounts(0, 0);

    expect($percentage->value)->toBe(0.0)
        ->and($percentage->ratio())->toBe(0.0);
});

test('attendance percentage rejects negative counts', function (): void {
    AttendancePercentage::fromCounts(-1, 10);
})->throws(InvalidArgumentException::class);

test('attendance percentage from float clamps into [0, 100]', function (): void {
    expect(AttendancePercentage::fromFloat(120.0)->value)->toBe(100.0)
        ->and(AttendancePercentage::fromFloat(-10.0)->value)->toBe(0.0)
        ->and(AttendancePercentage::fromFloat(75.1234, 1)->value)->toBe(75.1);
});

test('attendance percentage isBelow compares against a threshold', function (): void {
    expect(AttendancePercentage::fromFloat(60.0)->isBelow(75.0))->toBeTrue()
        ->and(AttendancePercentage::fromFloat(80.0)->isBelow(75.0))->toBeFalse();
});

test('risk score stays within [0, 100]', function (): void {
    expect((new RiskScore(0.0))->value)->toBe(0.0)
        ->and((new RiskScore(100.0))->value)->toBe(100.0);
});

test('risk score rejects out of range values', function (): void {
    new RiskScore(101.0);
})->throws(InvalidArgumentException::class);

test('risk score derives level and color from configuration', function (): void {
    expect((new RiskScore(10.0))->level())->toBe('low')
        ->and((new RiskScore(50.0))->level())->toBe('medium')
        ->and((new RiskScore(80.0))->level())->toBe('high')
        ->and((new RiskScore(10.0))->color())->toBe('success')
        ->and((new RiskScore(80.0))->color())->toBe('danger');
});

test('attendance period validates its window', function (): void {
    $period = AttendancePeriod::monthly(CarbonImmutable::parse('2026-09-15'));

    expect($period->startsOn->toDateString())->toBe('2026-09-01')
        ->and($period->endsOn->toDateString())->toBe('2026-09-30')
        ->and($period->type)->toBe(AttendancePeriod::TYPE_MONTHLY)
        ->and($period->days())->toBe(30)
        ->and($period->contains(CarbonImmutable::parse('2026-09-10')))->toBeTrue()
        ->and($period->contains(CarbonImmutable::parse('2026-10-01')))->toBeFalse();
});

test('attendance period rejects a start after its end', function (): void {
    new AttendancePeriod(
        CarbonImmutable::parse('2026-10-01'),
        CarbonImmutable::parse('2026-09-01'),
    );
})->throws(InvalidArgumentException::class);

test('attendance status value resolves classification from configuration', function (): void {
    $present = new AttendanceStatusValue(AttendanceStatus::Present);
    $absent = new AttendanceStatusValue(AttendanceStatus::Absent);
    $neutral = new AttendanceStatusValue(AttendanceStatus::Excused);

    expect($present->isPresent())->toBeTrue()
        ->and($absent->isAbsent())->toBeTrue()
        ->and($neutral->isNeutral())->toBeTrue()
        ->and($present->value())->toBe('present')
        ->and($present->label())->toBe('Présent')
        ->and($present->equals(new AttendanceStatusValue(AttendanceStatus::Present)))->toBeTrue()
        ->and($present->equals($absent))->toBeFalse()
        ->and((string) $present)->toBe('present');
});

test('timestamp parses values and computes minute deltas', function (): void {
    $start = new TimeStamp('2026-09-01 08:00:00');
    $late = new TimeStamp('2026-09-01 08:15:00');

    expect($start->toDateString())->toBe('2026-09-01')
        ->and($start->format('H:i'))->toBe('08:00')
        ->and($late->minutesAfter($start))->toBe(15)
        ->and($start->minutesAfter($late))->toBe(0);
});

test('correction reason enforces its fixed vocabulary', function (): void {
    expect(CorrectionReason::forType(CorrectionReason::TYPE_ERROR)->type)->toBe('data_entry_error')
        ->and(CorrectionReason::forType(CorrectionReason::TYPE_MEDICAL_CERTIFICATE)->typeLabel())->toBe('Certificat médical')
        ->and((new CorrectionReason())->type)->toBe(CorrectionReason::TYPE_OTHER);
});

test('correction reason rejects unknown types', function (): void {
    CorrectionReason::forType('not-a-real-type');
})->throws(InvalidArgumentException::class);

test('correction reason rejects overlong text', function (): void {
    new CorrectionReason(CorrectionReason::TYPE_OTHER, str_repeat('a', 501));
})->throws(InvalidArgumentException::class);
