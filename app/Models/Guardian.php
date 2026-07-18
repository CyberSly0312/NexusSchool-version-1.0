<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\Guardian\Enums\GuardianStatus;
use App\Domains\Guardian\Enums\PreferredContactMethod;
use App\Domains\Guardian\Enums\RelationshipType;
use App\Domains\School\Models\School;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Guardian extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'guardian_number',
        'first_name',
        'last_name',
        'middle_name',
        'preferred_name',
        'gender',
        'date_of_birth',
        'nationality',
        'relationship_type',
        'email',
        'phone_number',
        'alternate_phone',
        'occupation',
        'employer',
        'residential_address',
        'postal_address',
        'emergency_contact',
        'preferred_communication_language',
        'preferred_contact_method',
        'is_primary_guardian',
        'user_id',
        'notes',
        'status',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Guardian $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'is_primary_guardian' => 'boolean',
            'status' => GuardianStatus::class,
            'relationship_type' => RelationshipType::class,
            'preferred_contact_method' => PreferredContactMethod::class,
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
        return $query->where('status', GuardianStatus::Active);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', GuardianStatus::Archived);
    }

    public function scopeForSchool($query, string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary_guardian', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === GuardianStatus::Active;
    }

    public function isArchived(): bool
    {
        return $this->status === GuardianStatus::Archived;
    }

    public function isPrimary(): bool
    {
        return $this->is_primary_guardian;
    }
}
