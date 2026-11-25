<?php

declare(strict_types=1);

namespace App\Domains\User\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * The stronger password policy used whenever an administrator recovery or a
 * School Admin account is involved. The message is intentionally explicit and
 * French: no value, token or hash is ever echoed back.
 */
final class PrivilegedPasswordRule implements ValidationRule
{
    public const MESSAGE = 'Le mot de passe doit contenir au moins 12 caractères, une lettre minuscule, une lettre majuscule, un chiffre et un caractère spécial.';

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || mb_strlen($value) < 12) {
            $fail(self::MESSAGE);

            return;
        }

        $hasLower = preg_match('/[a-z]/', $value) === 1;
        $hasUpper = preg_match('/[A-Z]/', $value) === 1;
        $hasNumber = preg_match('/[0-9]/', $value) === 1;
        $hasSymbol = preg_match('/[^A-Za-z0-9]/', $value) === 1;

        if (! $hasLower || ! $hasUpper || ! $hasNumber || ! $hasSymbol) {
            $fail(self::MESSAGE);
        }
    }
}
