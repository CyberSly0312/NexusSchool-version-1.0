<?php

declare(strict_types=1);

use App\Domains\Academic\Domain\ValueObjects\DateRange;

test('range is inclusive on both boundaries', function (): void {
    $range = DateRange::fromStrings('2026-09-01', '2026-12-18');

    expect($range->contains(DateRange::fromStrings('2026-09-01', '2026-09-01')))->toBeTrue()
        ->and($range->contains(DateRange::fromStrings('2026-12-18', '2026-12-18')))->toBeTrue();
});

test('range contains a fully nested range', function (): void {
    $range = DateRange::fromStrings('2026-09-01', '2027-07-31');

    expect($range->contains(DateRange::fromStrings('2026-10-01', '2027-01-15')))->toBeTrue();
});

test('range rejects a range outside its boundaries', function (): void {
    $range = DateRange::fromStrings('2026-09-01', '2027-07-31');

    expect($range->contains(DateRange::fromStrings('2027-08-01', '2027-09-30')))->toBeFalse()
        ->and($range->contains(DateRange::fromStrings('2026-01-01', '2026-08-01')))->toBeFalse();
});

test('overlapping ranges overlap', function (): void {
    $range = DateRange::fromStrings('2026-09-01', '2026-12-18');

    expect($range->overlaps(DateRange::fromStrings('2026-12-01', '2027-01-15')))->toBeTrue()
        ->and($range->overlaps(DateRange::fromStrings('2026-08-15', '2026-10-01')))->toBeTrue();
});

test('touching boundaries do not overlap', function (): void {
    $range = DateRange::fromStrings('2026-09-01', '2026-12-18');

    expect($range->overlaps(DateRange::fromStrings('2026-12-18', '2027-01-15')))->toBeFalse()
        ->and($range->overlaps(DateRange::fromStrings('2026-08-01', '2026-09-01')))->toBeFalse();
});

test('rejects a range whose start is after its end', function (): void {
    DateRange::fromStrings('2026-12-18', '2026-09-01');
})->throws(InvalidArgumentException::class);

test('rejects an invalid date string', function (): void {
    DateRange::fromStrings('not-a-date', '2026-12-18');
})->throws(\Exception::class);
