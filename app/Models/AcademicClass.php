<?php

namespace App\Models;

use App\Domains\AcademicClass\Enums\AcademicClassStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class AcademicClass extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'academic_year_id',
        'program_id',
        'grade_level_id',
        'section_id',
        'name',
        'code',
        'description',
        'capacity',
        'display_order',
        'status',
        'is_active',
        'homeroom_teacher_id',
        'classroom',
        'classroom_id',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (AcademicClass $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'display_order' => 'integer',
            'status' => AcademicClassStatus::class,
            'is_active' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', AcademicClassStatus::Active);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', AcademicClassStatus::Archived);
    }

    public function scopeForAcademicYear($query, int $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    public function scopeEnabled($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === AcademicClassStatus::Active;
    }

    public function isArchived(): bool
    {
        return $this->status === AcademicClassStatus::Archived;
    }

    public function isEnabled(): bool
    {
        return $this->is_active === true;
    }

    public function hasCapacity(): bool
    {
        return $this->capacity > 0;
    }
}
