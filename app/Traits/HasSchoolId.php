<?php

declare(strict_types=1);

namespace App\Traits;

use App\Domains\School\Models\School;
use Illuminate\Database\Eloquent\Model;

trait HasSchoolId
{
    public static function bootHasSchoolId(): void
    {
        static::creating(function (Model $model) {
            if (empty($model->school_id)) {
                $model->school_id = tenant()?->id ?? School::first()?->id;
            }
        });
    }

    public function scopeForSchool($query, string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }
}
