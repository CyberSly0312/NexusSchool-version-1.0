<?php

declare(strict_types=1);

namespace App\Domains\Transport\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class StudentTransportAssignmentRules
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
            'student_id' => [$required, 'string'],
            'route_id' => [$required, 'string'],
            'pickup_stop_id' => ['nullable', 'string'],
            'dropoff_stop_id' => ['nullable', 'string'],
            'academic_year_id' => [$required, 'string'],
            'academic_term_id' => ['nullable', 'string'],
            'monthly_fee' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'in:active,inactive,suspended'],
            'enrolled_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
