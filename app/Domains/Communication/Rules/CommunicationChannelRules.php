<?php

declare(strict_types=1);

namespace App\Domains\Communication\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CommunicationChannelRules
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
            'channel_type' => [$required, 'string', 'in:email,sms,whatsapp,push,in_app'],
            'is_active' => ['nullable', 'boolean'],
            'config' => ['nullable', 'array'],
            'from_address' => ['nullable', 'string', 'email', 'max:255'],
            'from_name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
