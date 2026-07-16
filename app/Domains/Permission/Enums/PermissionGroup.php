<?php

namespace App\Domains\Permission\Enums;

enum PermissionGroup: string
{
    case Schools = 'school';
    case Users = 'user';
    case Roles = 'role';
    case Permissions = 'permission';
    case Academic = 'academic';
    case Students = 'student';
    case Teachers = 'teacher';
    case Admissions = 'admission';
    case Attendance = 'attendance';
    case Examinations = 'examination';
    case Grades = 'grade';
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

    public function label(): string
    {
        return match ($this) {
            self::Schools => 'Schools',
            self::Users => 'Users',
            self::Roles => 'Roles',
            self::Permissions => 'Permissions',
            self::Academic => 'Academic',
            self::Students => 'Students',
            self::Teachers => 'Teachers',
            self::Admissions => 'Admissions',
            self::Attendance => 'Attendance',
            self::Examinations => 'Examinations',
            self::Grades => 'Grades',
            self::Finance => 'Finance',
            self::Library => 'Library',
            self::HR => 'HR',
            self::Inventory => 'Inventory',
            self::Transport => 'Transport',
            self::Communication => 'Communication',
            self::Reports => 'Reports',
            self::Settings => 'Settings',
            self::Dashboard => 'Dashboard',
            self::AuditLogs => 'Audit Logs',
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
