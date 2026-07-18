<?php

declare(strict_types=1);

namespace App\Domains\Ai\Enums;

enum AiKnowledgeSourceStatus: string
{
    case Pending = 'pending';
    case Indexing = 'indexing';
    case Indexed = 'indexed';
    case Failed = 'failed';
    case Outdated = 'outdated';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Indexing => 'Indexing',
            self::Indexed => 'Indexed',
            self::Failed => 'Failed',
            self::Outdated => 'Outdated',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Indexing => 'info',
            self::Indexed => 'success',
            self::Failed => 'danger',
            self::Outdated => 'secondary',
        };
    }

    public static function labels(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->label(), self::cases()),
        );
    }

    public static function colors(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->color(), self::cases()),
        );
    }
}
