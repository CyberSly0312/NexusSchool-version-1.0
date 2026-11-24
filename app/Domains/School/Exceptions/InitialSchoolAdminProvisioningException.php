<?php

declare(strict_types=1);

namespace App\Domains\School\Exceptions;

use RuntimeException;

/**
 * Safe, presentation-ready failures for initial School Admin provisioning.
 *
 * Messages are intentionally generic and never contain passwords or invitation
 * tokens. The `reason` code lets the Filament layer map duplicate emails to the
 * email form field without parsing exception text.
 */
final class InitialSchoolAdminProvisioningException extends RuntimeException
{
    public const REASON_ACTOR = 'actor';
    public const REASON_SCHOOL = 'school';
    public const REASON_EXISTING_ADMIN = 'existing_admin';
    public const REASON_INPUT = 'input';
    public const REASON_DUPLICATE_EMAIL = 'duplicate_email';
    public const REASON_PROVISIONING = 'provisioning';

    public function __construct(
        string $message,
        public readonly string $reason,
    ) {
        parent::__construct($message);
    }

    public static function invalidActor(): self
    {
        return new self(
            'Seuls les administrateurs plateforme actifs peuvent créer un administrateur d’établissement.',
            self::REASON_ACTOR,
        );
    }

    public static function schoolNotAvailable(): self
    {
        return new self(
            'Cet établissement n’est pas disponible pour cette opération. Il doit être actif et posséder un domaine canonique.',
            self::REASON_SCHOOL,
        );
    }

    public static function schoolAdminAlreadyExists(): self
    {
        return new self(
            'Un administrateur existe déjà pour cet établissement. Utilisez la régénération du lien si nécessaire.',
            self::REASON_EXISTING_ADMIN,
        );
    }

    public static function invalidInput(): self
    {
        return new self(
            'Veuillez renseigner un prénom, un nom et une adresse e-mail valide.',
            self::REASON_INPUT,
        );
    }

    public static function duplicateEmail(): self
    {
        return new self(
            'Cette adresse e-mail est déjà associée à un compte, y compris un compte supprimé.',
            self::REASON_DUPLICATE_EMAIL,
        );
    }

    public static function provisioningFailed(): self
    {
        return new self(
            'Le compte n’a pas pu être créé. Aucune modification n’a été conservée.',
            self::REASON_PROVISIONING,
        );
    }
}
