<?php

namespace App\Domains\Permission\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PermissionRules
{
    public static function create(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'unique:permissions,name'],
            'guard_name' => ['sometimes', 'string', 'max:100'],
        ];
    }

    public static function update(?int $permissionId = null): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('permissions', 'name')->ignore($permissionId)],
            'guard_name' => ['sometimes', 'string', 'max:100'],
        ];
    }

    public static function validate(array $data, string $mode = 'create', ?int $permissionId = null): array
    {
        $rules = match ($mode) {
            'update' => self::update($permissionId),
            default => self::create(),
        };

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
