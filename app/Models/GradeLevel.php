<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\GradeLevel\Enums\EducationalCycle;
use App\Domains\GradeLevel\Enums\GradeLevelStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\School\Models\School;
use Illuminate\Support\Str;

class GradeLevel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'program_id',
        'name',
        'code',
        'short_name',
        'level_order',
        'educational_cycle',
        'description',
        'status',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (GradeLevel $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'level_order' => 'integer',
            'educational_cycle' => EducationalCycle::class,
            'status' => GradeLevelStatus::class,
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

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
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
        return $query->where('status', GradeLevelStatus::Active);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', GradeLevelStatus::Archived);
    }

    public function scopeForProgram($query, int $programId)
    {
        return $query->where('program_id', $programId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('level_order');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === GradeLevelStatus::Active;
    }

    public function isArchived(): bool
    {
        return $this->status === GradeLevelStatus::Archived;
    }
}
