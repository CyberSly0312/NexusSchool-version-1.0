<?php

declare(strict_types=1);

namespace App\Domains\Communication\Enums;

enum TemplateCategory: string
{
    case FeeReminder = 'fee_reminder';
    case AttendanceAlert = 'attendance_alert';
    case GradePublication = 'grade_publication';
    case LibraryReminder = 'library_reminder';
    case TransportUpdate = 'transport_update';
    case PayrollNotice = 'payroll_notice';
    case PasswordReset = 'password_reset';
    case Welcome = 'welcome';
    case General = 'general';

    public static function labels(): array
    {
        return [
            self::FeeReminder->value => 'Fee Reminder',
            self::AttendanceAlert->value => 'Attendance Alert',
            self::GradePublication->value => 'Grade Publication',
            self::LibraryReminder->value => 'Library Reminder',
            self::TransportUpdate->value => 'Transport Update',
            self::PayrollNotice->value => 'Payroll Notice',
            self::PasswordReset->value => 'Password Reset',
            self::Welcome->value => 'Welcome',
            self::General->value => 'General',
        ];
    }

    public static function colors(): array
    {
        return [
            self::FeeReminder->value => 'warning',
            self::AttendanceAlert->value => 'danger',
            self::GradePublication->value => 'success',
            self::LibraryReminder->value => 'info',
            self::TransportUpdate->value => 'primary',
            self::PayrollNotice->value => 'gray',
            self::PasswordReset->value => 'danger',
            self::Welcome->value => 'success',
            self::General->value => 'gray',
        ];
    }

    public static function icons(): array
    {
        return [
            self::FeeReminder->value => 'heroicon-o-currency-dollar',
            self::AttendanceAlert->value => 'heroicon-o-exclamation-triangle',
            self::GradePublication->value => 'heroicon-o-academic-cap',
            self::LibraryReminder->value => 'heroicon-o-book-open',
            self::TransportUpdate->value => 'heroicon-o-truck',
            self::PayrollNotice->value => 'heroicon-o-banknotes',
            self::PasswordReset->value => 'heroicon-o-key',
            self::Welcome->value => 'heroicon-o-hand-raised',
            self::General->value => 'heroicon-o-megaphone',
        ];
    }
}
