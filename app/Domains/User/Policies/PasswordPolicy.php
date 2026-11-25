<?php

declare(strict_types=1);

namespace App\Domains\User\Policies;

use App\Domains\User\Rules\PrivilegedPasswordRule;

/**
 * Single source of truth for password validation across Filament pages,
 * tenant controllers and application services.
 */
final class PasswordPolicy
{
    public const PERSONAL_MIN_LENGTH = 8;

    public const PRIVILEGED_MIN_LENGTH = 12;

    /**
     * @return list<mixed>
     */
    public static function personalRules(): array
    {
        return [
            'required',
            'string',
            'min:'.self::PERSONAL_MIN_LENGTH,
            'confirmed',
        ];
    }

    /**
     * @return list<mixed>
     */
    public static function privilegedRules(): array
    {
        return [
            'required',
            'string',
            'min:'.self::PRIVILEGED_MIN_LENGTH,
            'confirmed',
            new PrivilegedPasswordRule(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function validationMessages(string $field = 'new_password'): array
    {
        return [
            "{$field}.required" => 'Le nouveau mot de passe est obligatoire.',
            "{$field}.string" => 'Le nouveau mot de passe doit être une chaîne de caractères.',
            "{$field}.min" => 'Le nouveau mot de passe doit contenir au moins '.self::PERSONAL_MIN_LENGTH.' caractères.',
            "{$field}.confirmed" => 'La confirmation du nouveau mot de passe ne correspond pas.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function privilegedValidationMessages(string $field = 'password'): array
    {
        return [
            "{$field}.required" => 'Le mot de passe est obligatoire.',
            "{$field}.string" => 'Le mot de passe doit être une chaîne de caractères.',
            "{$field}.min" => 'Le mot de passe doit contenir au moins '.self::PRIVILEGED_MIN_LENGTH.' caractères.',
            "{$field}.confirmed" => 'La confirmation du mot de passe ne correspond pas.',
        ];
    }

    /**
     * Messages consumed by a Filament field (the field prefix is added by the
     * component itself).
     *
     * @return array<string, string>
     */
    public static function fieldValidationMessages(bool $privileged = false): array
    {
        $messages = $privileged
            ? self::privilegedValidationMessages()
            : self::validationMessages();

        $prefix = $privileged ? 'password.' : 'new_password.';

        $result = [];
        foreach ($messages as $key => $message) {
            $result[str_starts_with($key, $prefix) ? substr($key, strlen($prefix)) : $key] = $message;
        }

        return $result;
    }
}
