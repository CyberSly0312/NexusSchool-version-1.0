<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\ValueObjects;

use App\Domains\Attendance\Domain\Enums\AttendanceStatus;

/**
 * A recorded attendance status together with its effective classification
 * (present / absent / neutral) resolved from configuration (SPR-008).
 */
final readonly class AttendanceStatusValue
{
    public function __construct(
        public AttendanceStatus $status,
    ) {
    }

    public function isPresent(): bool
    {
        return $this->status->countsAsPresent();
    }

    public function isAbsent(): bool
    {
        return $this->status->countsAsAbsent();
    }

    public function isNeutral(): bool
    {
        return $this->status->isNeutral();
    }

    public function value(): string
    {
        return $this->status->value;
    }

    public function label(): string
    {
        return $this->status->label();
    }

    public function equals(self $other): bool
    {
        return $this->status === $other->status;
    }

    public function __toString(): string
    {
        return $this->status->value;
    }
}
