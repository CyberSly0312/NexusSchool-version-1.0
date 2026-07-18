<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\Subject\Enums\SubjectStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\School\Models\School;
use Illuminate\Support\Str;

class Subject extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'subject_category_id',
        'department_id',
        'program_id',
        'name',
        'code',
        'short_name',
        'description',
        'display_order',
        'color',
        'is_elective',
        'is_core_subject',
        'weekly_hours',
        'status',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Subject $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'display_order' => 'integer',
            'is_elective' => 'boolean',
            'is_core_subject' => 'boolean',
            'weekly_hours' => 'integer',
            'status' => SubjectStatus::class,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function subjectCategory(): BelongsTo
    {
        return $this->belongsTo(SubjectCategory::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', SubjectStatus::Active);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', SubjectStatus::Archived);
    }

    public function scopeForSchool($query, string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeForCategory($query, int $categoryId)
    {
        return $query->where('subject_category_id', $categoryId);
    }

    public function scopeForDepartment($query, int $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeForProgram($query, int $programId)
    {
        return $query->where('program_id', $programId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

    public function scopeCore($query)
    {
        return $query->where('is_core_subject', true);
    }

    public function scopeElective($query)
    {
        return $query->where('is_elective', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === SubjectStatus::Active;
    }

    public function isArchived(): bool
    {
        return $this->status === SubjectStatus::Archived;
    }

    public function isCore(): bool
    {
        return $this->is_core_subject;
    }

    public function isElective(): bool
    {
        return $this->is_elective;
    }
}
