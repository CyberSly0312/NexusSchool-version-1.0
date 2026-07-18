<?php

declare(strict_types=1);

namespace App\Domains\Workflow\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class WorkflowVersionRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'string'],
            'workflow_id' => ['required', 'string'],
            'changelog' => ['nullable', 'string'],
            'nodes' => ['required', 'array', 'min:1'],
            'nodes.*.id' => ['required', 'string'],
            'nodes.*.type' => ['required', 'string', 'in:start,end,decision,condition,approval,notification,delay,api_call,ai_action,database_action,script_placeholder'],
            'nodes.*.name' => ['required', 'string', 'max:200'],
            'edges' => ['required', 'array'],
            'edges.*.id' => ['required', 'string'],
            'edges.*.source' => ['required', 'string'],
            'edges.*.target' => ['required', 'string'],
            'settings' => ['nullable', 'array'],
        ];
    }

    public static function validate(array $data, string $mode = 'create', ?string $id = null): array
    {
        $rules = match ($mode) {
            default => self::create(),
        };

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
