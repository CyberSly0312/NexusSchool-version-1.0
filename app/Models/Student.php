<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\School\Models\School;
use App\Domains\Student\Enums\StudentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Student extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'student_number',
        'admission_number',
        'national_id',
        'first_name',
        'last_name',
        'middle_name',
        'preferred_name',
        'gender',
        'date_of_birth',
        'place_of_birth',
        'nationality',
        'blood_group',
        'religion',
        'photo',
        'email',
        'phone_number',
        'residential_address',
        'admission_date',
        'allergies',
        'medical_notes',
        'user_id',
        'notes',
        'status',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Student $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'admission_date' => 'date',
            'status' => StudentStatus::class,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', StudentStatus::Active);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', StudentStatus::Archived);
    }

    public function scopeForSchool($query, string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === StudentStatus::Active;
    }

    public function isArchived(): bool
    {
        return $this->status === StudentStatus::Archived;
    }

    public function isGraduated(): bool
    {
        return $this->status === StudentStatus::Graduated;
    }

    public function isSuspended(): bool
    {
        return $this->status === StudentStatus::Suspended;
    }
}
