<?php

declare(strict_types=1);

namespace App\Domains\Library\Enums;

enum ReservationStatus: string
{
    case Pending = 'pending';
    case Fulfilled = 'fulfilled';
    case Cancelled = 'cancelled';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Fulfilled => 'Fulfilled',
            self::Cancelled => 'Cancelled',
            self::Expired => 'Expired',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Fulfilled => 'success',
            self::Cancelled => 'gray',
            self::Expired => 'danger',
        };
    }
}
