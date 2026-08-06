<?php

declare(strict_types=1);

namespace App\Domains\Staff\Infrastructure\Models;

use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Teacher specialization of a Staff member. Kept as a supporting entity so
 * teacher-specific information never pollutes the Staff aggregate root.
 */
class TeacherProfile extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'teacher_profiles';

    protected $fillable = [
        'uuid',
        'school_id',
        'staff_id',
        'teaching_license_number',
        'teaching_license_valid_until',
        'is_homeroom_teacher',
        'max_weekly_hours',
        'preferred_teaching_levels',
        'teaching_subject_ids',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'teaching_license_valid_until' => 'date',
            'is_homeroom_teacher' => 'boolean',
            'max_weekly_hours' => 'integer',
            'preferred_teaching_levels' => 'array',
            'teaching_subject_ids' => 'array',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Staff\TeacherProfileFactory();
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function isHomeroomTeacher(): bool
    {
        return $this->is_homeroom_teacher;
    }
}
