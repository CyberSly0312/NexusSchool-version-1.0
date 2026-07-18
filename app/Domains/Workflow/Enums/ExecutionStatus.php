<?php

declare(strict_types=1);

namespace App\Domains\Workflow\Enums;

enum ExecutionStatus: string
{
    case Pending = 'pending';
    case Running = 'running';
    case Paused = 'paused';
    case WaitingApproval = 'waiting_approval';
    case Completed = 'completed';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
    case TimedOut = 'timed_out';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Running => 'Running',
            self::Paused => 'Paused',
            self::WaitingApproval => 'Waiting Approval',
            self::Completed => 'Completed',
            self::Failed => 'Failed',
            self::Cancelled => 'Cancelled',
            self::TimedOut => 'Timed Out',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::Running => 'info',
            self::Paused => 'warning',
            self::WaitingApproval => 'warning',
            self::Completed => 'success',
            self::Failed => 'danger',
            self::Cancelled => 'secondary',
            self::TimedOut => 'danger',
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

    public function isTerminal(): bool
    {
        return in_array($this, [self::Completed, self::Failed, self::Cancelled, self::TimedOut]);
    }

    public function isRetryable(): bool
    {
        return in_array($this, [self::Failed, self::TimedOut]);
    }
}
