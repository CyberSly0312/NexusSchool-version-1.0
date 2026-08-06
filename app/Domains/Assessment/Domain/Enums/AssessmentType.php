<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\Enums;

/**
 * Configurable assessment types (SPR-009).
 *
 * The twelve canonical types come from the nexus-assessment configuration and
 * can be extended by a school without touching code. `custom` is the free-form
 * type used for school-specific kinds of assessment.
 */
enum AssessmentType: string
{
    case Quiz = 'quiz';
    case Homework = 'homework';
    case Assignment = 'assignment';
    case Practical = 'practical';
    case Laboratory = 'laboratory';
    case Project = 'project';
    case Oral = 'oral';
    case Midterm = 'midterm';
    case FinalExam = 'final_exam';
    case ContinuousAssessment = 'continuous_assessment';
    case BehaviorAssessment = 'behavior_assessment';
    case Custom = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::Quiz => 'Quiz',
            self::Homework => 'Devoir maison',
            self::Assignment => 'Devoir',
            self::Practical => 'Pratique',
            self::Laboratory => 'Laboratoire',
            self::Project => 'Projet',
            self::Oral => 'Oral',
            self::Midterm => 'Examen partiel',
            self::FinalExam => 'Examen final',
            self::ContinuousAssessment => 'Évaluation continue',
            self::BehaviorAssessment => 'Évaluation comportementale',
            self::Custom => 'Personnalisé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Quiz => 'info',
            self::Homework => 'gray',
            self::Assignment => 'gray',
            self::Practical => 'warning',
            self::Laboratory => 'warning',
            self::Project => 'primary',
            self::Oral => 'info',
            self::Midterm => 'danger',
            self::FinalExam => 'danger',
            self::ContinuousAssessment => 'success',
            self::BehaviorAssessment => 'info',
            self::Custom => 'gray',
        };
    }

    /**
     * Whether this is the free-form type schools configure themselves.
     */
    public function isCustom(): bool
    {
        return $this === self::Custom;
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_map(
            static fn (self $type): string => $type->label(),
            self::cases(),
        );
    }
}
