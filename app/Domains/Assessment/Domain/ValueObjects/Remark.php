<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * Immutable teacher remark attached to a score (SPR-009). Free text bounded by
 * the configurable maximum length; empty and whitespace-only remarks collapse
 * to null so nullable columns stay tidy.
 */
final readonly class Remark
{
    public function __construct(
        public ?string $text,
    ) {
        if ($this->text !== null) {
            $trimmed = trim($this->text);

            if ($trimmed === '') {
                $this->text = null;
            }

            if ($this->text !== null) {
                $maxLength = (int) config('nexus-assessment.remarks.max_length', 1000);

                if (mb_strlen($this->text) > $maxLength) {
                    throw new InvalidArgumentException("The remark must not exceed {$maxLength} characters.");
                }
            }
        }
    }

    public function isEmpty(): bool
    {
        return $this->text === null || $this->text === '';
    }

    public function __toString(): string
    {
        return $this->text ?? '';
    }
}
