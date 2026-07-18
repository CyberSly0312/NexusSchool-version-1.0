<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\Classroom\Enums\ClassroomStatus;
use App\Domains\Classroom\Enums\RoomType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\School\Models\School;
use Illuminate\Support\Str;

class Classroom extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'building',
        'floor',
        'capacity',
        'room_type',
        'description',
        'display_order',
        'status',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Classroom $model) {
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
            'status' => ClassroomStatus::class,
            'room_type' => RoomType::class,
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
        return $query->where('status', ClassroomStatus::Active);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', ClassroomStatus::Archived);
    }

    public function scopeForSchool($query, string $schoolId)
    {
        return $query->where('school_id', $schoolId);
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
        return $this->status === ClassroomStatus::Active;
    }

    public function isArchived(): bool
    {
        return $this->status === ClassroomStatus::Archived;
    }
}
