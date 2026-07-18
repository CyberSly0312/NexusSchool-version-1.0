<?php

declare(strict_types=1);

namespace App\Domains\Transport\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RouteRules
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
            'description' => ['nullable', 'string'],
            'distance_km' => ['nullable', 'numeric', 'min:0'],
            'estimated_duration_minutes' => ['nullable', 'integer', 'min:1'],
            'fare' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'in:active,inactive,archived'],
        ];
    }
}
