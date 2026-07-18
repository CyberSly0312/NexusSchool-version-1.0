<?php

declare(strict_types=1);

namespace App\Domains\System\Enums;

enum ActivityType: string
{
    case System = 'system';
    case User = 'user';
    case Auth = 'auth';
    case Data = 'data';
    case Notification = 'notification';
    case Api = 'api';

    public function label(): string
    {
        return match ($this) {
            self::System => 'System',
            self::User => 'User',
            self::Auth => 'Authentication',
            self::Data => 'Data',
            self::Notification => 'Notification',
            self::Api => 'API',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::System => 'gray',
            self::User => 'info',
            self::Auth => 'warning',
            self::Data => 'primary',
            self::Notification => 'success',
            self::Api => 'secondary',
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
