<?php

namespace App\Domains\Gradebook\Enums;

enum LetterGrade: string
{
    case APlus = 'A+';
    case A = 'A';
    case AMinus = 'A-';
    case BPlus = 'B+';
    case B = 'B';
    case BMinus = 'B-';
    case CPlus = 'C+';
    case C = 'C';
    case CMinus = 'C-';
    case DPlus = 'D+';
    case D = 'D';
    case F = 'F';

    public function label(): string
    {
        return $this->value;
    }

    public function gradePoint(): float
    {
        return match ($this) {
            self::APlus => 4.0,
            self::A => 4.0,
            self::AMinus => 3.7,
            self::BPlus => 3.3,
            self::B => 3.0,
            self::BMinus => 2.7,
            self::CPlus => 2.3,
            self::C => 2.0,
            self::CMinus => 1.7,
            self::DPlus => 1.3,
            self::D => 1.0,
            self::F => 0.0,
        };
    }

    public static function fromPercentage(float $percentage): self
    {
        return match (true) {
            $percentage >= 97 => self::APlus,
            $percentage >= 93 => self::A,
            $percentage >= 90 => self::AMinus,
            $percentage >= 87 => self::BPlus,
            $percentage >= 83 => self::B,
            $percentage >= 80 => self::BMinus,
            $percentage >= 77 => self::CPlus,
            $percentage >= 73 => self::C,
            $percentage >= 70 => self::CMinus,
            $percentage >= 67 => self::DPlus,
            $percentage >= 60 => self::D,
            default => self::F,
        };
    }

    public static function gradePointFromPercentage(float $percentage): float
    {
        return self::fromPercentage($percentage)->gradePoint();
    }
}
