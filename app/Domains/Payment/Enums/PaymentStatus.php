<?php

declare(strict_types=1);

namespace App\Domains\Payment\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Partial = 'partial';
    case Overdue = 'overdue';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Paid => 'Payé',
            self::Partial => 'Partiel',
            self::Overdue => 'En retard',
            self::Cancelled => 'Annulé',
            self::Refunded => 'Remboursé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Paid => 'success',
            self::Partial => 'info',
            self::Overdue => 'danger',
            self::Cancelled => 'gray',
            self::Refunded => 'primary',
        };
    }
}
