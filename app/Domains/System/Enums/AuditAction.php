<?php

declare(strict_types=1);

namespace App\Domains\System\Enums;

enum AuditAction: string
{
    case Created = 'created';
    case Updated = 'updated';
    case Deleted = 'deleted';
    case Restored = 'restored';
    case Viewed = 'viewed';
    case Exported = 'exported';
    case Login = 'login';
    case Logout = 'logout';
    case FailedLogin = 'failed_login';

    public function label(): string
    {
        return match ($this) {
            self::Created => 'Created',
            self::Updated => 'Updated',
            self::Deleted => 'Deleted',
            self::Restored => 'Restored',
            self::Viewed => 'Viewed',
            self::Exported => 'Exported',
            self::Login => 'Login',
            self::Logout => 'Logout',
            self::FailedLogin => 'Failed Login',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Created => 'success',
            self::Updated => 'info',
            self::Deleted => 'danger',
            self::Restored => 'warning',
            self::Viewed => 'gray',
            self::Exported => 'secondary',
            self::Login => 'success',
            self::Logout => 'secondary',
            self::FailedLogin => 'danger',
        };
    }

    public static function labels(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->label(), self::cases()),
        );
    }
}
