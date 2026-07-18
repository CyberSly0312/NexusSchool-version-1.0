<?php

declare(strict_types=1);

namespace App\Domains\Ai\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AiProviderRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'driver' => ['required', 'string', 'in:openai,anthropic,gemini,azure_openai,ollama'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'configuration' => ['nullable', 'array'],
            'supported_models' => ['nullable', 'array'],
            'default_model' => ['nullable', 'string', 'max:255'],
            'rate_limit_per_minute' => ['sometimes', 'integer', 'min:1', 'max:10000'],
            'daily_token_limit' => ['nullable', 'integer', 'min:1'],
            'monthly_token_limit' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public static function update(?string $id = null): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'driver' => ['sometimes', 'required', 'string', 'in:openai,anthropic,gemini,azure_openai,ollama'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'configuration' => ['nullable', 'array'],
            'supported_models' => ['nullable', 'array'],
            'default_model' => ['nullable', 'string', 'max:255'],
            'rate_limit_per_minute' => ['sometimes', 'integer', 'min:1', 'max:10000'],
            'daily_token_limit' => ['nullable', 'integer', 'min:1'],
            'monthly_token_limit' => ['nullable', 'integer', 'min:1'],
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
