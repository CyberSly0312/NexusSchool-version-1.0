<?php

declare(strict_types=1);

namespace App\Domains\Workflow\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class WorkflowTriggerRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'string'],
            'workflow_id' => ['required', 'string'],
            'type' => ['required', 'string', 'in:event,time,manual,api,scheduled'],
            'name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'event_name' => ['required_if:type,event', 'nullable', 'string', 'max:255'],
            'conditions' => ['nullable', 'array'],
            'schedule' => ['nullable', 'array'],
            'metadata' => ['nullable', 'array'],
            'cooldown_seconds' => ['sometimes', 'integer', 'min:0'],
            'max_executions_per_hour' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public static function update(?string $id = null): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'event_name' => ['nullable', 'string', 'max:255'],
            'conditions' => ['nullable', 'array'],
            'schedule' => ['nullable', 'array'],
            'metadata' => ['nullable', 'array'],
            'cooldown_seconds' => ['sometimes', 'integer', 'min:0'],
            'max_executions_per_hour' => ['nullable', 'integer', 'min:1'],
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
