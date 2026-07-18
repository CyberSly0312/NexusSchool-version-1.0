<?php

declare(strict_types=1);

namespace App\Domains\Api\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ApiTokenRules
{
    public static function create(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'abilities' => ['nullable', 'array'],
            'expires_at' => ['sometimes', 'nullable', 'date', 'after:now'],
            'user_id' => ['sometimes', 'nullable', 'integer'],
        ];
    }

    public static function update(?string $id = null): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'abilities' => ['nullable', 'array'],
            'expires_at' => ['sometimes', 'nullable', 'date', 'after:now'],
            'user_id' => ['sometimes', 'nullable', 'integer'],
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
