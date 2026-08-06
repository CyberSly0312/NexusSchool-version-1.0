<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\ValueObjects;

use Carbon\CarbonImmutable;

/**
 * An inclusive date window used for monthly, semester or yearly attendance
 * calculations (SPR-008).
 */
final readonly class AttendancePeriod
{
    public const TYPE_MONTHLY = 'monthly';

    public const TYPE_SEMESTER = 'semester';

    public const TYPE_YEARLY = 'yearly';

    public function __construct(
        public CarbonImmutable $startsOn,
        public CarbonImmutable $endsOn,
        public string $type = self::TYPE_MONTHLY,
    ) {
        if ($startsOn->greaterThan($endsOn)) {
            throw new \InvalidArgumentException('AttendancePeriod start cannot be after its end.');
        }
    }

    public static function monthly(CarbonImmutable $month): self
    {
        return new self(
            $month->startOfMonth(),
            $month->endOfMonth(),
            self::TYPE_MONTHLY,
        );
    }

    public static function semester(CarbonImmutable $startsOn, CarbonImmutable $endsOn): self
    {
        return new self($startsOn, $endsOn, self::TYPE_SEMESTER);
    }

    public static function yearly(CarbonImmutable $startsOn, CarbonImmutable $endsOn): self
    {
        return new self($startsOn, $endsOn, self::TYPE_YEARLY);
    }

    public function days(): int
    {
        return (int) $this->startsOn->diffInDays($this->endsOn) + 1;
    }

    public function contains(CarbonImmutable $date): bool
    {
        return ! $date->lessThan($this->startsOn->startOfDay())
            && ! $date->greaterThan($this->endsOn->endOfDay());
    }
}
