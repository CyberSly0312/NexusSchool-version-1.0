<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Enums;

/**
 * Canonical document types produced by the Academic Communication Engine
 * (SPR-012). The engine is the single publication layer of NexusSchool: every
 * official academic document is generated here, never inside a business engine.
 *
 * Extensibility: a school may define additional custom types without touching
 * the code — see the `custom` case and `config/nexus-academic-communication.php`.
 */
enum DocumentType: string
{
    case ReportCard = 'report_card';
    case CouncilMinutes = 'council_minutes';
    case ParentSummary = 'parent_summary';
    case TeacherSummary = 'teacher_summary';
    case StudentSummary = 'student_summary';
    case AcademicStatistics = 'academic_statistics';
    case SchoolChronicle = 'school_chronicle';
    case Transcript = 'transcript';
    case AttendanceReport = 'attendance_report';
    case ConductReport = 'conduct_report';
    case Certificate = 'certificate';
    case EnrollmentConfirmation = 'enrollment_confirmation';
    case OfficialLetter = 'official_letter';
    case CustomDocument = 'custom_document';

    /**
     * Short alphabetic code embedded in the permanent document identity
     * (e.g. `NS-2027-SCH001-RC-000000145`).
     */
    public function code(): string
    {
        return match ($this) {
            self::ReportCard => 'RC',
            self::CouncilMinutes => 'CM',
            self::ParentSummary => 'PS',
            self::TeacherSummary => 'TS',
            self::StudentSummary => 'SS',
            self::AcademicStatistics => 'ST',
            self::SchoolChronicle => 'SC',
            self::Transcript => 'TR',
            self::AttendanceReport => 'AR',
            self::ConductReport => 'CR',
            self::Certificate => 'CE',
            self::EnrollmentConfirmation => 'EC',
            self::OfficialLetter => 'OL',
            self::CustomDocument => 'CU',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::ReportCard => 'Bulletin scolaire',
            self::CouncilMinutes => 'Procès-verbal du conseil',
            self::ParentSummary => 'Synthèse parents',
            self::TeacherSummary => 'Synthèse enseignants',
            self::StudentSummary => 'Synthèse élève',
            self::AcademicStatistics => 'Statistiques académiques',
            self::SchoolChronicle => 'Chronique scolaire',
            self::Transcript => 'Relevé de notes',
            self::AttendanceReport => 'Rapport de présence',
            self::ConductReport => 'Rapport de conduite',
            self::Certificate => 'Certificat',
            self::EnrollmentConfirmation => 'Confirmation d\'inscription',
            self::OfficialLetter => 'Lettre officielle',
            self::CustomDocument => 'Document personnalisé',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::ReportCard => 'heroicon-o-academic-cap',
            self::CouncilMinutes => 'heroicon-o-clipboard-document-list',
            self::ParentSummary => 'heroicon-o-users',
            self::TeacherSummary => 'heroicon-o-user-group',
            self::StudentSummary => 'heroicon-o-user',
            self::AcademicStatistics => 'heroicon-o-chart-bar',
            self::SchoolChronicle => 'heroicon-o-newspaper',
            self::Transcript => 'heroicon-o-document-duplicate',
            self::AttendanceReport => 'heroicon-o-calendar',
            self::ConductReport => 'heroicon-o-shield-check',
            self::Certificate => 'heroicon-o-document-check',
            self::EnrollmentConfirmation => 'heroicon-o-clipboard-check',
            self::OfficialLetter => 'heroicon-o-paper-airplane',
            self::CustomDocument => 'heroicon-o-document',
        };
    }

    /**
     * Whether this document type typically targets an individual student.
     */
    public function isStudentScoped(): bool
    {
        return in_array($this, [
            self::ReportCard,
            self::ParentSummary,
            self::StudentSummary,
            self::Transcript,
            self::AttendanceReport,
            self::ConductReport,
            self::Certificate,
            self::EnrollmentConfirmation,
        ], true);
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
