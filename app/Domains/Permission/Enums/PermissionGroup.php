<?php

declare(strict_types=1);

namespace App\Domains\Permission\Enums;

enum PermissionGroup: string
{
    case Schools = 'school';
    case Users = 'user';
    case Roles = 'role';
    case Permissions = 'permission';
    case Academic = 'academic';
    case AcademicClass = 'academic_class';
    case AcademicTerm = 'academic_term';
    case AcademicYear = 'academic_year';
    case Students = 'student';
    case StudentEnrollment = 'student_enrollment';
    case Teachers = 'teacher';
    case Guardians = 'guardian';
    case Admissions = 'admission';
    case Attendance = 'attendance';
    case Examinations = 'examination';
    case Gradebook = 'gradebook';
    case Grades = 'grade';
    case GradeLevel = 'grade_level';
    case Sections = 'section';
    case Classrooms = 'classroom';
    case Departments = 'department';
    case Programs = 'program';
    case Subjects = 'subject';
    case SubjectCategories = 'subject_category';
    case SubjectCoefficients = 'subject_coefficient';
    case Finance = 'finance';
    case Library = 'library';
    case HR = 'hr';
    case Inventory = 'inventory';
    case Transport = 'transport';
    case Communication = 'communication';
    case Reports = 'report';
    case Settings = 'setting';
    case Dashboard = 'dashboard';
    case AuditLogs = 'audit_log';
    case Ai = 'ai';
    case Analytics = 'analytics';
    case Api = 'api';
    case Backup = 'backup';
    case MaintenanceWindow = 'maintenance_window';
    case NotificationChannel = 'notification_channel';
    case Workflows = 'workflow';
    case AcademicCalendarYear = 'academic_calendar_year';
    case AcademicCalendarTerm = 'academic_calendar_term';
    case AcademicCalendarEvent = 'academic_calendar_event';
    case EducationLevel = 'education_level';
    case AcademicProgram = 'academic_program';
    case Stream = 'stream';
    case SchoolClass = 'school_class';
    case ClassSection = 'class_section';
    case SubjectGroup = 'subject_group';
    case AcademicSubject = 'academic_subject';
    case Coefficient = 'coefficient';

    public function label(): string
    {
        return match ($this) {
            self::Schools => 'Écoles',
            self::Users => 'Utilisateurs',
            self::Roles => 'Rôles',
            self::Permissions => 'Permissions',
            self::Academic => 'Académique',
            self::AcademicClass => 'Classes',
            self::AcademicTerm => 'Périodes',
            self::AcademicYear => 'Années scolaires',
            self::Students => 'Élèves',
            self::StudentEnrollment => 'Inscriptions',
            self::Teachers => 'Enseignants',
            self::Guardians => 'Parents / Tuteurs',
            self::Admissions => 'Admissions',
            self::Attendance => 'Présences',
            self::Examinations => 'Examens',
            self::Gradebook => 'Carnet de notes',
            self::Grades => 'Notes',
            self::GradeLevel => 'Niveaux',
            self::Sections => 'Sections',
            self::Classrooms => 'Salles',
            self::Departments => 'Départements',
            self::Programs => 'Programmes',
            self::Subjects => 'Matières',
            self::SubjectCategories => 'Catégories de matières',
            self::SubjectCoefficients => 'Coefficients',
            self::Finance => 'Finance',
            self::Library => 'Bibliothèque',
            self::HR => 'Ressources humaines',
            self::Inventory => 'Inventaire',
            self::Transport => 'Transport',
            self::Communication => 'Communication',
            self::Reports => 'Rapports',
            self::Settings => 'Paramètres',
            self::Dashboard => 'Tableau de bord',
            self::AuditLogs => 'Journaux d\'audit',
            self::Ai => 'Intelligence artificielle',
            self::Analytics => 'Analytiques',
            self::Api => 'API',
            self::Backup => 'Sauvegardes',
            self::MaintenanceWindow => 'Fenêtres de maintenance',
            self::NotificationChannel => 'Canaux de notification',
            self::Workflows => 'Flux de travail',
            self::AcademicCalendarYear => 'Années scolaires',
            self::AcademicCalendarTerm => 'Périodes',
            self::AcademicCalendarEvent => 'Événements',
            self::EducationLevel => 'Niveaux',
            self::AcademicProgram => 'Programmes',
            self::Stream => 'Filières',
            self::SchoolClass => 'Classes',
            self::ClassSection => 'Sections',
            self::SubjectGroup => 'Groupes de matières',
            self::AcademicSubject => 'Matières',
            self::Coefficient => 'Coefficients',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn (self $group) => $group->label(),
            self::cases(),
        );
    }
}
