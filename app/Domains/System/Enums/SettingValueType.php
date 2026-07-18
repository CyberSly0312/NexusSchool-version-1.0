<?php

declare(strict_types=1);

namespace App\Domains\System\Enums;

enum SettingValueType: string
{
    case String = 'string';
    case Integer = 'integer';
    case Float = 'float';
    case Boolean = 'boolean';
    case Json = 'json';
    case Encrypted = 'encrypted';

    public function label(): string
    {
        return match ($this) {
            self::String => 'String',
            self::Integer => 'Integer',
            self::Float => 'Float',
            self::Boolean => 'Boolean',
            self::Json => 'JSON',
            self::Encrypted => 'Encrypted',
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
