<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DashboardRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'string', 'exists:tenants,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'string', 'in:school,academic,finance,hr,library,transport,custom'],
            'is_default' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'layout' => ['nullable', 'array'],
            'created_by' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    public static function update(?string $id = null): array
    {
        return [
            'school_id' => ['sometimes', 'required', 'string', 'exists:tenants,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['sometimes', 'required', 'string', 'in:school,academic,finance,hr,library,transport,custom'],
            'is_default' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'layout' => ['nullable', 'array'],
            'created_by' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    public static function validate(array $data, string $mode = 'create', ?string $id = null): array
    {
        $rules = match ($mode) {
            'update' => self::update($id),
            default => self::create(),
        };

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
