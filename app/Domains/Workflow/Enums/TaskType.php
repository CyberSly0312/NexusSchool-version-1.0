<?php

declare(strict_types=1);

namespace App\Domains\Workflow\Enums;

enum TaskType: string
{
    case Approval = 'approval';
    case Review = 'review';
    case Notification = 'notification';
    case DataEntry = 'data_entry';
    case ApiCall = 'api_call';
    case AiAction = 'ai_action';
    case DatabaseAction = 'database_action';
    case ScriptPlaceholder = 'script_placeholder';

    public function label(): string
    {
        return match ($this) {
            self::Approval => 'Approval',
            self::Review => 'Review',
            self::Notification => 'Notification',
            self::DataEntry => 'Data Entry',
            self::ApiCall => 'API Call',
            self::AiAction => 'AI Action',
            self::DatabaseAction => 'Database Action',
            self::ScriptPlaceholder => 'Script Placeholder',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Approval => 'danger',
            self::Review => 'warning',
            self::Notification => 'info',
            self::DataEntry => 'primary',
            self::ApiCall => 'success',
            self::AiAction => 'primary',
            self::DatabaseAction => 'warning',
            self::ScriptPlaceholder => 'gray',
        };
    }

    public static function labels(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->label(), self::cases()),
        );
    }

    public function requiresUserInteraction(): bool
    {
        return in_array($this, [self::Approval, self::Review, self::DataEntry]);
    }
}
