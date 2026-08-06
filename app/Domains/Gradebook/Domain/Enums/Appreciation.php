<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\Enums;

/**
 * French appreciation of an average on the school grading scale (SPR-010).
 *
 * The thresholds are expressed on a 20-point scale: 16 = Très bien,
 * 14 = Bien, 12 = Assez bien, 10 = Passable, below 10 = Insuffisant.
 */
enum Appreciation: string
{
    case TresBien = 'Très bien';
    case Bien = 'Bien';
    case AssezBien = 'Assez bien';
    case Passable = 'Passable';
    case Insuffisant = 'Insuffisant';

    public function label(): string
    {
        return $this->value;
    }

    public function color(): string
    {
        return match ($this) {
            self::TresBien => 'success',
            self::Bien => 'success',
            self::AssezBien => 'info',
            self::Passable => 'warning',
            self::Insuffisant => 'danger',
        };
    }

    /**
     * Maps an average expressed on the configured scale (default 20) onto its
     * appreciation.
     */
    public static function fromAverage(float $average, int $scale = 20): self
    {
        if ($scale <= 0) {
            $scale = 20;
        }

        $scaled = ($average / $scale) * 20;

        return match (true) {
            $scaled >= 16 => self::TresBien,
            $scaled >= 14 => self::Bien,
            $scaled >= 12 => self::AssezBien,
            $scaled >= 10 => self::Passable,
            default => self::Insuffisant,
        };
    }
}
