<?php

declare(strict_types=1);

namespace App\Domains\Ai\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AiConversationRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'string'],
            'ai_provider_id' => ['nullable', 'string'],
            'user_id' => ['nullable', 'string'],
            'title' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'in:active,archived,summarized,closed'],
            'context_type' => ['nullable', 'string', 'max:100'],
            'context_id' => ['nullable', 'string', 'max:36'],
            'model' => ['nullable', 'string', 'max:255'],
            'system_prompt' => ['nullable', 'string'],
            'context_window_limit' => ['sometimes', 'integer', 'min:1000', 'max:1000000'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public static function update(?string $id = null): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'in:active,archived,summarized,closed'],
            'system_prompt' => ['nullable', 'string'],
            'summary' => ['nullable', 'string'],
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
