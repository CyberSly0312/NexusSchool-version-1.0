<?php

declare(strict_types=1);

namespace App\Domains\Transport\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RouteStopRules
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
            'route_id' => [$required, 'string'],
            'name' => [$required, 'string', 'max:255'],
            'stop_order' => [$required, 'integer', 'min:1'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'estimated_time_minutes' => ['nullable', 'integer', 'min:0'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
