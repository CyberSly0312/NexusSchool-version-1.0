<?php

declare(strict_types=1);

namespace App\Domains\Communication\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class NotificationRules
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
            'sender_id' => ['nullable'],
            'template_id' => ['nullable'],
            'type' => [$required, 'string', 'max:100'],
            'title' => [$required, 'string', 'max:255'],
            'body' => [$required, 'string'],
            'data' => ['nullable', 'array'],
            'status' => ['nullable', 'string', 'in:pending,queued,sent,delivered,failed,cancelled'],
            'channel' => ['nullable', 'string', 'in:email,sms,whatsapp,push,in_app'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
            'recipient_ids' => [$required, 'array'],
            'recipient_ids.*' => [],
        ];
    }
}
