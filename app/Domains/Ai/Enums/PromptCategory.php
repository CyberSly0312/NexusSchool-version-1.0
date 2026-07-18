<?php

declare(strict_types=1);

namespace App\Domains\Ai\Enums;

enum PromptCategory: string
{
    case StudentAssistant = 'student_assistant';
    case TeacherAssistant = 'teacher_assistant';
    case ParentAssistant = 'parent_assistant';
    case FinanceAssistant = 'finance_assistant';
    case HrAssistant = 'hr_assistant';
    case AnalyticsAssistant = 'analytics_assistant';
    case TechSupportAssistant = 'tech_support_assistant';
    case AdministratorAssistant = 'administrator_assistant';

    public function label(): string
    {
        return match ($this) {
            self::StudentAssistant => 'Student Assistant',
            self::TeacherAssistant => 'Teacher Assistant',
            self::ParentAssistant => 'Parent Assistant',
            self::FinanceAssistant => 'Finance Assistant',
            self::HrAssistant => 'HR Assistant',
            self::AnalyticsAssistant => 'Analytics Assistant',
            self::TechSupportAssistant => 'Technical Support Assistant',
            self::AdministratorAssistant => 'Administrator Assistant',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::StudentAssistant => 'info',
            self::TeacherAssistant => 'success',
            self::ParentAssistant => 'warning',
            self::FinanceAssistant => 'primary',
            self::HrAssistant => 'secondary',
            self::AnalyticsAssistant => 'danger',
            self::TechSupportAssistant => 'gray',
            self::AdministratorAssistant => 'info',
        };
    }

    public static function labels(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->label(), self::cases()),
        );
    }

    public static function colors(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->color(), self::cases()),
        );
    }
}
