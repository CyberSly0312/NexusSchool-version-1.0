<?php

declare(strict_types=1);

use App\Domains\Gradebook\Domain\Enums\Appreciation;
use App\Domains\Gradebook\Domain\Enums\LetterGrade;

test('letter grade maps percentage bounds onto the expected grades', function (): void {
    expect(LetterGrade::fromPercentage(95))->toBe(LetterGrade::A)
        ->and(LetterGrade::fromPercentage(90))->toBe(LetterGrade::A)
        ->and(LetterGrade::fromPercentage(89.99))->toBe(LetterGrade::AMinus)
        ->and(LetterGrade::fromPercentage(80))->toBe(LetterGrade::AMinus)
        ->and(LetterGrade::fromPercentage(79.99))->toBe(LetterGrade::BPlus)
        ->and(LetterGrade::fromPercentage(70))->toBe(LetterGrade::BPlus)
        ->and(LetterGrade::fromPercentage(60))->toBe(LetterGrade::B)
        ->and(LetterGrade::fromPercentage(55))->toBe(LetterGrade::CPlus)
        ->and(LetterGrade::fromPercentage(50))->toBe(LetterGrade::C)
        ->and(LetterGrade::fromPercentage(45))->toBe(LetterGrade::D)
        ->and(LetterGrade::fromPercentage(40))->toBe(LetterGrade::DMinus)
        ->and(LetterGrade::fromPercentage(39.99))->toBe(LetterGrade::F);
});

test('letter grades expose grade points and appreciations', function (): void {
    expect(LetterGrade::A->gradePoint())->toBe(4.0)
        ->and(LetterGrade::AMinus->gradePoint())->toBe(3.7)
        ->and(LetterGrade::BPlus->gradePoint())->toBe(3.3)
        ->and(LetterGrade::C->gradePoint())->toBe(2.0)
        ->and(LetterGrade::F->gradePoint())->toBe(0.0)
        ->and(LetterGrade::A->appreciation())->toBe('Très bien')
        ->and(LetterGrade::F->appreciation())->toBe('Très insuffisant');
});

test('the 50 percent threshold separates passes from fails', function (): void {
    expect(LetterGrade::fromPercentage(60)->passes())->toBeTrue()
        ->and(LetterGrade::fromPercentage(50)->passes())->toBeTrue()
        ->and(LetterGrade::fromPercentage(49.99)->passes())->toBeFalse()
        ->and(LetterGrade::F->passes())->toBeFalse();
});

test('appreciation derives from the school scale average', function (): void {
    expect(Appreciation::fromAverage(16))->toBe(Appreciation::TresBien)
        ->and(Appreciation::fromAverage(14))->toBe(Appreciation::Bien)
        ->and(Appreciation::fromAverage(12))->toBe(Appreciation::AssezBien)
        ->and(Appreciation::fromAverage(10))->toBe(Appreciation::Passable)
        ->and(Appreciation::fromAverage(9.99))->toBe(Appreciation::Insuffisant);
});
