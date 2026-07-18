<?php

declare(strict_types=1);

namespace App\Domains\Transport\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DriverRules
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
            'first_name' => [$required, 'string', 'max:100'],
            'last_name' => [$required, 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => [$required, 'string', 'max:20'],
            'license_number' => [$required, 'string', 'max:50'],
            'license_expiry' => [$required, 'date', 'after:today'],
            'license_category' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'emergency_contact_name' => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'national_id' => ['nullable', 'string', 'max:50'],
            'photo' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:available,on_trip,on_leave,inactive'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
