<?php

declare(strict_types=1);

namespace App\Domains\Ai\Enums;

enum AiUsageLogStatus: string
{
    case Success = 'success';
    case Failed = 'failed';
    case RateLimited = 'rate_limited';
    case QuotaExceeded = 'quota_exceeded';
    case Cached = 'cached';

    public function label(): string
    {
        return match ($this) {
            self::Success => 'Success',
            self::Failed => 'Failed',
            self::RateLimited => 'Rate Limited',
            self::QuotaExceeded => 'Quota Exceeded',
            self::Cached => 'Cached',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Success => 'success',
            self::Failed => 'danger',
            self::RateLimited => 'warning',
            self::QuotaExceeded => 'danger',
            self::Cached => 'info',
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
