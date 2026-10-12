<?php

declare(strict_types=1);

namespace App\Domains\AcademicPolicy\Domain\Enums;

/**
 * Stored type of an academic policy value (SPR-009.5).
 *
 * The value is stored as JSON and normalized back onto this declared type when
 * read, so a consumer never receives a value whose runtime type does not match
 * the policy's declared type.
 */
enum AcademicPolicyValueType: string
{
    case String = 'string';
    case Integer = 'integer';
    case Float = 'float';
    case Boolean = 'boolean';
    case Json = 'json';

    public function label(): string
    {
        return match ($this) {
            self::String => 'Texte',
            self::Integer => 'Entier',
            self::Float => 'Décimal',
            self::Boolean => 'Oui / Non',
            self::Json => 'Structure',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function options(): array
    {
        return array_map(
            static fn (self $type): string => $type->label(),
            self::cases(),
        );
    }
}
