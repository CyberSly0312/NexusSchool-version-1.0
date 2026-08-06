<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Enums;

/**
 * Visibility scopes of the Academic Communication Engine (SPR-012).
 *
 * Visibility is policy-driven and configurable at the school level: the
 * PublicationPolicy carries a VisibilityPolicy that maps every scope to the
 * document sections it may see. Parents and students are always self-scoped —
 * they only ever see their own children / themselves.
 */
enum VisibilityScope: string
{
    case Director = 'director';
    case Secretary = 'secretary';
    case Teacher = 'teacher';
    case Parent = 'parent';
    case Student = 'student';
    case Inspector = 'inspector';

    public function label(): string
    {
        return match ($this) {
            self::Director => 'Directeur',
            self::Secretary => 'Secrétaire',
            self::Teacher => 'Enseignant',
            self::Parent => 'Parent',
            self::Student => 'Élève',
            self::Inspector => 'Inspecteur',
        };
    }

    public function isSelfScoped(): bool
    {
        return $this === self::Parent || $this === self::Student;
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_map(
            static fn (self $scope): string => $scope->label(),
            self::cases(),
        );
    }
}
