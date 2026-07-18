<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\Section\Enums\SectionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\School\Models\School;
use Illuminate\Support\Str;

class Section extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'grade_level_id',
        'name',
        'code',
        'description',
        'order',
        'status',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Section $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'status' => SectionStatus::class,
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

    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class);
    }

    public function academicClasses(): HasMany
    {
        return $this->hasMany(AcademicClass::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', SectionStatus::Active);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', SectionStatus::Archived);
    }

    public function scopeForGradeLevel($query, int $gradeLevelId)
    {
        return $query->where('grade_level_id', $gradeLevelId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === SectionStatus::Active;
    }

    public function isArchived(): bool
    {
        return $this->status === SectionStatus::Archived;
    }
}
