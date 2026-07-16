<?php

namespace App\Models;

use App\Domains\AcademicTerm\Enums\AcademicTermStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class AcademicTerm extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'academic_year_id',
        'school_id',
        'name',
        'code',
        'start_date',
        'end_date',
        'order',
        'description',
        'status',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (AcademicTerm $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'order' => 'integer',
            'status' => AcademicTermStatus::class,
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

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', AcademicTermStatus::Active);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', AcademicTermStatus::Draft);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', AcademicTermStatus::Archived);
    }

    public function scopeForYear($query, int $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('start_date');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === AcademicTermStatus::Active;
    }

    public function isDraft(): bool
    {
        return $this->status === AcademicTermStatus::Draft;
    }

    public function isArchived(): bool
    {
        return $this->status === AcademicTermStatus::Archived;
    }
}
