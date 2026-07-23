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
            self::Approval => 'Approbation',
            self::Review => 'Révision',
            self::Notification => 'Notification',
            self::DataEntry => 'Saisie de données',
            self::ApiCall => 'Appel API',
            self::AiAction => 'Action IA',
            self::DatabaseAction => 'Action base de données',
            self::ScriptPlaceholder => 'Placeholder de script',
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
