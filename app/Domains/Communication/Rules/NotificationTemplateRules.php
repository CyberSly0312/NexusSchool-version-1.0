<?php

declare(strict_types=1);

namespace App\Domains\Communication\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class NotificationTemplateRules
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
            'name' => [$required, 'string', 'max:255'],
            'slug' => [$required, 'string', 'max:255', 'lowercase'],
            'category' => [$required, 'string', 'in:fee_reminder,attendance_alert,grade_publication,library_reminder,transport_update,payroll_notice,password_reset,welcome,general'],
            'channel' => ['nullable', 'string', 'in:email,sms,whatsapp,push,in_app'],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => [$required, 'string'],
            'variables' => ['nullable', 'array'],
            'variables.*' => ['string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
