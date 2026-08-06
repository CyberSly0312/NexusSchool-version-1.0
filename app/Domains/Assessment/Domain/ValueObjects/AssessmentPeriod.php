<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\ValueObjects;

use Carbon\CarbonImmutable;
use InvalidArgumentException;

/**
 * Immutable date window an assessment is scheduled over (SPR-009). Used to
 * scope statistics and bulk operations to a term or a custom window.
 */
final readonly class AssessmentPeriod
{
    public const TYPE_CUSTOM = 'custom';
    public const TYPE_TERM = 'term';

    public function __construct(
        public CarbonImmutable $startsOn,
        public CarbonImmutable $endsOn,
        public string $type = self::TYPE_CUSTOM,
    ) {
        if ($startsOn->greaterThan($endsOn)) {
            throw new InvalidArgumentException('The assessment period start must not be after its end.');
        }
    }

    public static function forTerm(CarbonImmutable $startsOn, CarbonImmutable $endsOn): self
    {
        return new self($startsOn, $endsOn, self::TYPE_TERM);
    }

    public function contains(CarbonImmutable $date): bool
    {
        return $date->between($this->startsOn, $this->endsOn);
    }

    public function days(): int
    {
        return (int) $this->startsOn->diffInDays($this->endsOn) + 1;
    }
}
