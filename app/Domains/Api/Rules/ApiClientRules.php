<?php

declare(strict_types=1);

namespace App\Domains\Api\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ApiClientRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'callback_url' => ['nullable', 'url', 'max:500'],
            'scopes' => ['nullable', 'array'],
            'rate_limit_per_minute' => ['sometimes', 'integer', 'min:1', 'max:10000'],
            'rate_limit_per_day' => ['sometimes', 'integer', 'min:1', 'max:1000000'],
        ];
    }

    public static function update(?string $id = null): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'callback_url' => ['nullable', 'url', 'max:500'],
            'scopes' => ['nullable', 'array'],
            'rate_limit_per_minute' => ['sometimes', 'integer', 'min:1', 'max:10000'],
            'rate_limit_per_day' => ['sometimes', 'integer', 'min:1', 'max:1000000'],
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
