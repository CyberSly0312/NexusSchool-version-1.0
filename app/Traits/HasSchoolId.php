<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * @property string $school_id
 */
trait HasSchoolId
{
    public static function bootHasSchoolId(): void
    {
        static::creating(function (self $model) {
            if (empty($model->school_id) && tenant()) {
                $model->school_id = tenant()->id;
            }
        });
    }

    public function scopeForSchool($query, string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function getSchoolId(): string
    {
        return $this->school_id;
    }
}
