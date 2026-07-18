<?php

declare(strict_types=1);

namespace App\Domains\Workflow\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AutomationRuleRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'string'],
            'name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
            'priority' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'trigger_type' => ['required', 'string', 'in:event,time,manual,api,scheduled'],
            'event_name' => ['required_if:trigger_type,event', 'nullable', 'string', 'max:255'],
            'conditions' => ['nullable', 'array'],
            'actions' => ['required', 'array', 'min:1'],
            'actions.*.type' => ['required', 'string'],
            'cooldown_seconds' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    public static function update(?string $id = null): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
            'priority' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'trigger_type' => ['sometimes', 'required', 'string', 'in:event,time,manual,api,scheduled'],
            'event_name' => ['nullable', 'string', 'max:255'],
            'conditions' => ['nullable', 'array'],
            'actions' => ['sometimes', 'required', 'array', 'min:1'],
            'cooldown_seconds' => ['sometimes', 'integer', 'min:0'],
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
