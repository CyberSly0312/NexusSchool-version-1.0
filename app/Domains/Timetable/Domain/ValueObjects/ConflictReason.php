<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\ValueObjects;

use App\Domains\Timetable\Domain\Enums\ConflictType;

/**
 * Human-readable description of why an entry collides with another entry or
 * with an availability constraint. Produced by the conflict engine.
 */
final readonly class ConflictReason
{
    public function __construct(
        public ConflictType $type,
        public string $message,
        public ?string $detail = null,
    ) {
    }

    public static function from(
        ConflictType $type,
        string $message,
        ?string $detail = null,
    ): self {
        return new self($type, $message, $detail);
    }

    public function __toString(): string
    {
        return $this->detail !== null
            ? sprintf('%s — %s', $this->message, $this->detail)
            : $this->message;
    }
}
