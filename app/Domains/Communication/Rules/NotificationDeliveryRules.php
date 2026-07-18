<?php

declare(strict_types=1);

namespace App\Domains\Communication\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class NotificationDeliveryRules
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
            'notification_id' => [$required],
            'recipient_id' => [$required],
            'channel' => [$required, 'string', 'in:email,sms,whatsapp,push,in_app'],
            'status' => ['nullable', 'string', 'in:pending,sent,delivered,failed,bounced'],
            'error_message' => ['nullable', 'string'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
