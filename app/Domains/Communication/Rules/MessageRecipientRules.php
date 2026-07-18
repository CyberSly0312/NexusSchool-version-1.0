<?php

declare(strict_types=1);

namespace App\Domains\Communication\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MessageRecipientRules
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
            'message_id' => [$required],
            'recipient_id' => [$required],
            'status' => ['nullable', 'string', 'in:pending,sent,delivered,failed,bounced'],
        ];
    }
}
