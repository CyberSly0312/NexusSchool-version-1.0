<?php

declare(strict_types=1);

namespace App\Domains\Workflow\Enums;

enum NodeType: string
{
    case Start = 'start';
    case End = 'end';
    case Decision = 'decision';
    case Condition = 'condition';
    case Approval = 'approval';
    case Notification = 'notification';
    case Delay = 'delay';
    case ApiCall = 'api_call';
    case AiAction = 'ai_action';
    case DatabaseAction = 'database_action';
    case ScriptPlaceholder = 'script_placeholder';

    public function label(): string
    {
        return match ($this) {
            self::Start => 'Début',
            self::End => 'Fin',
            self::Decision => 'Décision',
            self::Condition => 'Condition',
            self::Approval => 'Approbation',
            self::Notification => 'Notification',
            self::Delay => 'Retard',
            self::ApiCall => 'Appel API',
            self::AiAction => 'Action IA',
            self::DatabaseAction => 'Action base de données',
            self::ScriptPlaceholder => 'Placeholder de script',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Start => 'success',
            self::End => 'secondary',
            self::Decision => 'warning',
            self::Condition => 'info',
            self::Approval => 'danger',
            self::Notification => 'primary',
            self::Delay => 'gray',
            self::ApiCall => 'info',
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
}
