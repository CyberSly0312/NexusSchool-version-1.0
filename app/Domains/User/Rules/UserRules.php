<?php

namespace App\Domains\User\Rules;

use App\Domains\User\Enums\UserStatus;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserRules
{
    public static function create(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:50'],
            'avatar' => ['nullable', 'string', 'max:2048'],
            'school_id' => ['nullable', 'string', 'exists:schools,id'],
            'status' => ['nullable', Rule::enum(UserStatus::class)],
        ];
    }

    public static function update(?string $userId = null): array
    {
        return [
            'first_name' => ['sometimes', 'required', 'string', 'max:100'],
            'last_name' => ['sometimes', 'required', 'string', 'max:100'],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['sometimes', 'nullable', 'string', 'min:8', 'confirmed'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'avatar' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'school_id' => ['sometimes', 'nullable', 'string', 'exists:schools,id'],
            'status' => ['sometimes', 'nullable', Rule::enum(UserStatus::class)],
        ];
    }

    public static function validate(array $data, string $mode = 'create', ?string $userId = null): array
    {
        $rules = match ($mode) {
            'update' => self::update($userId),
            default => self::create(),
        };

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
