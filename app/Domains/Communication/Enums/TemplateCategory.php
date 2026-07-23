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
            self::FeeReminder->value => 'Rappel de frais',
            self::AttendanceAlert->value => 'Alerte de présence',
            self::GradePublication->value => 'Publication de notes',
            self::LibraryReminder->value => 'Rappel de bibliothèque',
            self::TransportUpdate->value => 'Mise à jour transport',
            self::PayrollNotice->value => 'Avis de paie',
            self::PasswordReset->value => 'Réinitialisation de mot de passe',
            self::Welcome->value => 'Bienvenue',
            self::General->value => 'Général',
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
