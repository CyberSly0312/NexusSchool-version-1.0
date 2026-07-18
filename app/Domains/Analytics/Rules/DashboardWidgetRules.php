<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DashboardWidgetRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'string', 'exists:tenants,id'],
            'dashboard_id' => ['required', 'string', 'exists:dashboards,id'],
            'title' => ['required', 'string', 'max:255'],
            'widget_type' => ['required', 'string', 'in:chart,metric,table,progress,list,custom'],
            'data_source' => ['required', 'string', 'max:255'],
            'config' => ['nullable', 'array'],
            'position' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public static function update(?string $id = null): array
    {
        return [
            'school_id' => ['sometimes', 'required', 'string', 'exists:tenants,id'],
            'dashboard_id' => ['sometimes', 'required', 'string', 'exists:dashboards,id'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'widget_type' => ['sometimes', 'required', 'string', 'in:chart,metric,table,progress,list,custom'],
            'data_source' => ['sometimes', 'required', 'string', 'max:255'],
            'config' => ['nullable', 'array'],
            'position' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public static function validate(array $data, string $mode = 'create', ?string $id = null): array
    {
        $rules = match ($mode) {
            'update' => self::update($id),
            default => self::create(),
        };

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
