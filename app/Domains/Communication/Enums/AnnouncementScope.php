<?php

declare(strict_types=1);

namespace App\Domains\Communication\Enums;

enum AnnouncementScope: string
{
    case SchoolWide = 'school_wide';
    case Department = 'department';
    case GradeLevel = 'grade_level';
    case ClassLevel = 'class';
    case Staff = 'staff';
    case Parents = 'parents';
    case Individual = 'individual';

    public static function labels(): array
    {
        return [
            self::SchoolWide->value => 'School-Wide',
            self::Department->value => 'Department',
            self::GradeLevel->value => 'Grade Level',
            self::ClassLevel->value => 'Class',
            self::Staff->value => 'Staff',
            self::Parents->value => 'Parents',
            self::Individual->value => 'Individual',
        ];
    }

    public static function colors(): array
    {
        return [
            self::SchoolWide->value => 'primary',
            self::Department->value => 'info',
            self::GradeLevel->value => 'success',
            self::ClassLevel->value => 'warning',
            self::Staff->value => 'gray',
            self::Parents->value => 'danger',
            self::Individual->value => 'info',
        ];
    }

    public static function icons(): array
    {
        return [
            self::SchoolWide->value => 'heroicon-o-building-office',
            self::Department->value => 'heroicon-o-user-group',
            self::GradeLevel->value => 'heroicon-o-academic-cap',
            self::ClassLevel->value => 'heroicon-o-users',
            self::Staff->value => 'heroicon-o-briefcase',
            self::Parents->value => 'heroicon-o-heart',
            self::Individual->value => 'heroicon-o-user',
        ];
    }
}
