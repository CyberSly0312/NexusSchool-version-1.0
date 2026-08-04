<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Domain\Enums;

/**
 * Outcome of the end-of-level assessment leading to graduation.
 */
enum GraduationDecision: string
{
    case Graduated = 'graduated';
    case Conditional = 'conditional';

    public function label(): string
    {
        return match ($this) {
            self::Graduated => 'Diplômé',
            self::Conditional => 'Diplômé sous conditions',
        };
    }

    public function closesEnrollment(): bool
    {
        return true;
    }
}
