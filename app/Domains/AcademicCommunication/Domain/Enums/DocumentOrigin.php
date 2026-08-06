<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Enums;

/**
 * Origin of an AcademicDocument (SPR-012). The engine consumes validated data
 * from business engines through Contracts and Domain Events; the origin records
 * which canonical flow produced the document for traceability and auditing.
 */
enum DocumentOrigin: string
{
    case Manual = 'manual';
    case Generated = 'generated';
    case ReportCard = 'report_card';
    case CouncilMinutes = 'council_minutes';
    case ParentSummary = 'parent_summary';
    case TeacherSummary = 'teacher_summary';
    case StudentSummary = 'student_summary';
    case SchoolChronicle = 'school_chronicle';

    public function label(): string
    {
        return match ($this) {
            self::Manual => 'Manuel',
            self::Generated => 'Généré automatiquement',
            self::ReportCard => 'Bulletin',
            self::CouncilMinutes => 'Conseil de classe',
            self::ParentSummary => 'Synthèse parents',
            self::TeacherSummary => 'Synthèse enseignants',
            self::StudentSummary => 'Synthèse élève',
            self::SchoolChronicle => 'Chronique scolaire',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_map(
            static fn (self $origin): string => $origin->label(),
            self::cases(),
        );
    }
}
