<?php

declare(strict_types=1);

namespace App\Domains\Role\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RoleRules
{
    public static function create(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'unique:roles,name'],
        ];
    }

    public static function update(?int $roleId = null): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('roles', 'name')->ignore($roleId)],
        ];
    }

    public static function validate(array $data, string $mode = 'create', ?int $roleId = null): array
    {
        $rules = match ($mode) {
            'update' => self::update($roleId),
            default => self::create(),
        };

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
