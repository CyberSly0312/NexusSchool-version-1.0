<?php

declare(strict_types=1);

namespace App\Domains\Ai\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AiMessageRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'string'],
            'ai_conversation_id' => ['required', 'string'],
            'role' => ['required', 'string', 'in:system,user,assistant,function'],
            'content' => ['required', 'string'],
            'model' => ['nullable', 'string', 'max:255'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public static function validate(array $data, string $mode = 'create', ?string $id = null): array
    {
        $rules = self::create();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
