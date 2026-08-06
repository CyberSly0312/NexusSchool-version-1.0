<?php

declare(strict_types=1);

namespace App\Domains\Staff\Infrastructure\Models;

use App\Domains\Staff\Domain\Enums\AssignmentType;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Time-aware assignment of a staff member to a department, a position or a
 * campus (multi-campus support). Assignments never rewrite the immutable
 * employment history; they layer on top of it.
 */
class Assignment extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'assignments';

    protected $fillable = [
        'uuid',
        'school_id',
        'staff_id',
        'employment_id',
        'assignment_type',
        'department_id',
        'position_id',
        'campus_school_id',
        'assignment_start_date',
        'assignment_end_date',
        'is_current',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'assignment_type' => AssignmentType::class,
            'assignment_start_date' => 'date',
            'assignment_end_date' => 'date',
            'is_current' => 'boolean',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Staff\AssignmentFactory();
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function employment(): BelongsTo
    {
        return $this->belongsTo(Employment::class, 'employment_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Department::class, 'department_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeOfType($query, AssignmentType|string $type)
    {
        return $query->where('assignment_type', $type instanceof AssignmentType ? $type->value : $type);
    }

    public function scopeForStaff($query, int $staffId)
    {
        return $query->where('staff_id', $staffId);
    }

    public function isCurrent(): bool
    {
        return $this->is_current;
    }
}
