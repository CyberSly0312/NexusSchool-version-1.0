<?php

declare(strict_types=1);

namespace App\Domains\Ai\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AiPromptTemplateRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:student_assistant,teacher_assistant,parent_assistant,finance_assistant,hr_assistant,analytics_assistant,tech_support_assistant,administrator_assistant'],
            'description' => ['nullable', 'string'],
            'system_prompt' => ['required', 'string'],
            'user_prompt_template' => ['required', 'string'],
            'variables' => ['nullable', 'array'],
            'status' => ['sometimes', 'string', 'in:active,inactive,draft,archived'],
            'target_model' => ['nullable', 'string', 'max:255'],
            'temperature' => ['sometimes', 'numeric', 'min:0', 'max:2'],
            'max_tokens' => ['sometimes', 'integer', 'min:1', 'max:128000'],
            'top_p' => ['nullable', 'numeric', 'min:0', 'max:1'],
        ];
    }

    public static function update(?string $id = null): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'category' => ['sometimes', 'required', 'string'],
            'description' => ['nullable', 'string'],
            'system_prompt' => ['sometimes', 'required', 'string'],
            'user_prompt_template' => ['sometimes', 'required', 'string'],
            'variables' => ['nullable', 'array'],
            'status' => ['sometimes', 'string'],
            'target_model' => ['nullable', 'string', 'max:255'],
            'temperature' => ['sometimes', 'numeric', 'min:0', 'max:2'],
            'max_tokens' => ['sometimes', 'integer', 'min:1', 'max:128000'],
            'top_p' => ['nullable', 'numeric', 'min:0', 'max:1'],
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
