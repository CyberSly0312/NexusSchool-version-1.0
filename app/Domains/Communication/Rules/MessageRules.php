<?php

declare(strict_types=1);

namespace App\Domains\Communication\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MessageRules
{
    public static function validate(array $data, string $mode = 'create'): array
    {
        $rules = self::rules($mode);
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        return $validator->validated();
    }

    public static function rules(string $mode = 'create'): array
    {
        $required = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'school_id' => [$required, 'string'],
            'sender_id' => [$required],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => [$required, 'string'],
            'type' => ['nullable', 'string', 'in:direct,broadcast'],
            'status' => ['nullable', 'string', 'in:draft,sent,delivered,archived'],
            'recipient_ids' => [$required, 'array', 'min:1'],
            'recipient_ids.*' => [],
        ];
    }
}
