<?php

declare(strict_types=1);

namespace App\Domains\Library\Enums;

enum FineStatus: string
{
    case Pending = 'pending';
    case PartiallyPaid = 'partially_paid';
    case Paid = 'paid';
    case Waived = 'waived';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::PartiallyPaid => 'Partially Paid',
            self::Paid => 'Paid',
            self::Waived => 'Waived',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'danger',
            self::PartiallyPaid => 'warning',
            self::Paid => 'success',
            self::Waived => 'info',
        };
    }
}
