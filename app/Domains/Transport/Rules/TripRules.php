<?php

declare(strict_types=1);

namespace App\Domains\Transport\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TripRules
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
            'vehicle_id' => [$required, 'string'],
            'driver_id' => [$required, 'string'],
            'route_id' => [$required, 'string'],
            'direction' => [$required, 'string', 'in:to_school,from_school'],
            'academic_date' => [$required, 'date'],
            'departure_time' => ['nullable', 'date_format:H:i'],
            'arrival_time' => ['nullable', 'date_format:H:i'],
            'actual_departure' => ['nullable', 'date'],
            'actual_arrival' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:scheduled,started,completed,cancelled,archived'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
