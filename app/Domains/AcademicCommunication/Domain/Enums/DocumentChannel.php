<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Enums;

/**
 * Publication channels of the Academic Communication Engine (SPR-012).
 *
 * All channels are abstracted behind the PublicationChannel contract. New
 * channels (Email, SMS, Push Notification) are added by implementing the
 * contract and enabling the channel in the school's PublicationChannel — never
 * by redesigning the engine. The portal channels render the document through
 * the HTML renderer; pdf_export produces the official immutable file.
 */
enum DocumentChannel: string
{
    case InternalPortal = 'internal_portal';
    case ParentPortal = 'parent_portal';
    case TeacherPortal = 'teacher_portal';
    case StudentPortal = 'student_portal';
    case PdfExport = 'pdf_export';
    case Email = 'email';
    case Sms = 'sms';
    case PushNotification = 'push_notification';

    public function label(): string
    {
        return match ($this) {
            self::InternalPortal => 'Portail interne',
            self::ParentPortal => 'Portail parents',
            self::TeacherPortal => 'Portail enseignants',
            self::StudentPortal => 'Portail élèves',
            self::PdfExport => 'Export PDF',
            self::Email => 'E-mail',
            self::Sms => 'SMS',
            self::PushNotification => 'Notification push',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::InternalPortal => 'heroicon-o-home',
            self::ParentPortal => 'heroicon-o-users',
            self::TeacherPortal => 'heroicon-o-user-group',
            self::StudentPortal => 'heroicon-o-user',
            self::PdfExport => 'heroicon-o-document-arrow-down',
            self::Email => 'heroicon-o-envelope',
            self::Sms => 'heroicon-o-chat-bubble-left-right',
            self::PushNotification => 'heroicon-o-bell',
        };
    }

    /**
     * Future channels that are defined but not yet delivered by this engine.
     */
    public function isImplemented(): bool
    {
        return in_array($this, [
            self::InternalPortal,
            self::ParentPortal,
            self::TeacherPortal,
            self::StudentPortal,
            self::PdfExport,
        ], true);
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_map(
            static fn (self $channel): string => $channel->label(),
            self::cases(),
        );
    }
}
