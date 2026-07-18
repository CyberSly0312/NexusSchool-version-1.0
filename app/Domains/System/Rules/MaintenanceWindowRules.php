<?php

declare(strict_types=1);

namespace App\Domains\System\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MaintenanceWindowRules
{
    public static function create(): array
    {
        return [
            'name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'type' => ['sometimes', 'required', 'string', 'in:scheduled,emergency,rolling'],
            'is_readonly' => ['sometimes', 'boolean'],
            'starts_at' => ['required', 'date', 'after:now'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'affected_services' => ['nullable', 'array'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public static function update(?string $id = null): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'is_readonly' => ['sometimes', 'boolean'],
            'starts_at' => ['sometimes', 'required', 'date'],
            'ends_at' => ['sometimes', 'required', 'date', 'after:starts_at'],
            'affected_services' => ['nullable', 'array'],
            'metadata' => ['nullable', 'array'],
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
