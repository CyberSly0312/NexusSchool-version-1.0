<?php

declare(strict_types=1);

namespace App\Domains\Department\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DepartmentRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'exists:tenants,id'],
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:20'],
            'order' => ['sometimes', 'integer', 'min:0'],
            'head_id' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['sometimes', 'string', 'in:active,archived'],
        ];
    }

    public static function update(?int $departmentId = null): array
    {
        return [
            'school_id' => ['sometimes', 'required', 'exists:tenants,id'],
            'name' => ['sometimes', 'required', 'string', 'max:150'],
            'code' => ['sometimes', 'required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:20'],
            'order' => ['sometimes', 'integer', 'min:0'],
            'head_id' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['sometimes', 'string', 'in:active,archived'],
        ];
    }

    public static function validate(array $data, string $mode = 'create', ?int $departmentId = null): array
    {
        $rules = match ($mode) {
            'update' => self::update($departmentId),
            default => self::create(),
        };

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
