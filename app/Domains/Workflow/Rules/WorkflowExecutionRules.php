<?php

declare(strict_types=1);

namespace App\Domains\Workflow\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class WorkflowExecutionRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'string'],
            'workflow_id' => ['required', 'string'],
            'trigger_id' => ['nullable', 'string'],
            'context' => ['nullable', 'array'],
            'variables' => ['nullable', 'array'],
            'idempotency_key' => ['nullable', 'string', 'max:255'],
            'initiated_by' => ['nullable', 'string'],
            'initiated_by_type' => ['nullable', 'string', 'max:50'],
        ];
    }

    public static function validate(array $data, string $mode = 'create'): array
    {
        $validator = Validator::make($data, self::create());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
