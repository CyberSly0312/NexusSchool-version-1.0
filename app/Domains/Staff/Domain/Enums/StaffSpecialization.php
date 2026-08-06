<?php

declare(strict_types=1);

namespace App\Domains\Staff\Domain\Enums;

/**
 * Professional specialization of a staff member. Teacher is a specialization
 * of Staff, never a separate aggregate root (SPR-006).
 */
enum StaffSpecialization: string
{
    case Teacher = 'teacher';
    case Director = 'director';
    case Secretary = 'secretary';
    case Accountant = 'accountant';
    case Librarian = 'librarian';
    case Registrar = 'registrar';
    case Administrator = 'administrator';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Teacher => 'Enseignant',
            self::Director => 'Directeur',
            self::Secretary => 'Secrétaire',
            self::Accountant => 'Comptable',
            self::Librarian => 'Bibliothécaire',
            self::Registrar => 'Scolarité',
            self::Administrator => 'Administrateur',
            self::Other => 'Autre',
        };
    }

    public function isTeacher(): bool
    {
        return $this === self::Teacher;
    }
}
