<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\ValueObjects;

/**
 * Immutable 0-100 risk score produced by the risk engine (SPR-008). A higher
 * score means higher risk of dropout / chronic absenteeism.
 */
final readonly class RiskScore
{
    public const LEVEL_LOW = 'low';

    public const LEVEL_MEDIUM = 'medium';

    public const LEVEL_HIGH = 'high';

    public function __construct(
        public float $value,
    ) {
        if ($value < 0.0 || $value > 100.0) {
            throw new \InvalidArgumentException('Risk score must be within [0, 100].');
        }
    }

    public function level(): string
    {
        // Levels are configured from lowest to highest; iterate in reverse so
        // the strictest matching band wins (e.g. 70+ is high, not low).
        $levels = array_reverse((array) config('nexus-attendance.risk.levels', []), true);

        foreach ($levels as $level => $config) {
            if ($this->value >= (float) ($config['min'] ?? 0.0)) {
                return $level;
            }
        }

        return self::LEVEL_LOW;
    }

    public function color(): string
    {
        $levels = (array) config('nexus-attendance.risk.levels', []);

        return (string) ($levels[$this->level()]['color'] ?? 'success');
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
