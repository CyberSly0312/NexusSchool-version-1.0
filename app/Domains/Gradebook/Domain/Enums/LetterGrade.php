<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\Enums;

/**
 * Letter grade computed from a term percentage (SPR-010).
 *
 * The bounds follow the conventional scale used by the school system (a 20
 * point scale, 10/20 being the passing threshold, i.e. 50 %). The letter and
 * its grade point are derived from the percentage so a gradebook never stores
 * them out of sync.
 */
enum LetterGrade: string
{
    case A = 'A';
    case AMinus = 'A-';
    case BPlus = 'B+';
    case B = 'B';
    case CPlus = 'C+';
    case C = 'C';
    case D = 'D';
    case DMinus = 'D-';
    case F = 'F';

    public function label(): string
    {
        return $this->value;
    }

    public function color(): string
    {
        return match ($this) {
            self::A => 'success',
            self::AMinus => 'success',
            self::BPlus => 'success',
            self::B => 'info',
            self::CPlus => 'info',
            self::C => 'warning',
            self::D, self::DMinus, self::F => 'danger',
        };
    }

    /**
     * The equivalent on the 4.0 GPA scale.
     */
    public function gradePoint(): float
    {
        return match ($this) {
            self::A => 4.0,
            self::AMinus => 3.7,
            self::BPlus => 3.3,
            self::B => 3.0,
            self::CPlus => 2.5,
            self::C => 2.0,
            self::D => 1.5,
            self::DMinus => 1.0,
            self::F => 0.0,
        };
    }

    /**
     * The French appreciation matching the 20-point thresholds.
     */
    public function appreciation(): string
    {
        return match ($this) {
            self::A => 'Très bien',
            self::AMinus => 'Très bien',
            self::BPlus => 'Bien',
            self::B => 'Bien',
            self::CPlus => 'Assez bien',
            self::C => 'Passable',
            self::D => 'Insuffisant',
            self::DMinus => 'Insuffisant',
            self::F => 'Très insuffisant',
        };
    }

    /**
     * A percentage is a pass when it reaches the 10/20 threshold (50 %).
     */
    public function passes(): bool
    {
        return $this !== self::D && $this !== self::DMinus && $this !== self::F;
    }

    /**
     * Maps a percentage (0–100) onto its letter grade.
     */
    public static function fromPercentage(float $percentage): self
    {
        return match (true) {
            $percentage >= 90 => self::A,
            $percentage >= 80 => self::AMinus,
            $percentage >= 70 => self::BPlus,
            $percentage >= 60 => self::B,
            $percentage >= 55 => self::CPlus,
            $percentage >= 50 => self::C,
            $percentage >= 45 => self::D,
            $percentage >= 40 => self::DMinus,
            default => self::F,
        };
    }
}
