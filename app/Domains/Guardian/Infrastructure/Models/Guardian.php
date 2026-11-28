<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Infrastructure\Models;

use App\Domains\Guardian\Domain\Enums\GuardianStatus;
use App\Domains\Guardian\Domain\Enums\PreferredContactMethod;
use App\Domains\School\Models\School;
use App\Models\User;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Aggregate root of the Guardian domain (SPR-005).
 *
 * A Guardian is a person who may (or may not) be linked to one or several
 * students. The links themselves are first-class StudentGuardianRelationship
 * entities carrying the business meaning (relationship type, primary flag,
 * emergency contact, authorized pickup, …). Invariants are enforced by the
 * application actions, never by the model.
 *
 * @property GuardianStatus $status
 */
class Guardian extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'guardians';

    protected $fillable = [
        'uuid',
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

    protected function casts(): array
    {
        return [
            'status' => GuardianStatus::class,
            'preferred_contact_method' => PreferredContactMethod::class,
            'date_of_birth' => 'date',
            'is_primary_guardian' => 'boolean',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Guardian\GuardianFactory();
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([$this->first_name, $this->last_name])));
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function relationships(): HasMany
    {
        return $this->hasMany(StudentGuardianRelationship::class, 'guardian_id');
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

    public function scopeByStatus($query, GuardianStatus|string $status)
    {
        return $query->where('status', $status instanceof GuardianStatus ? $status->value : $status);
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

    public function isImmutable(): bool
    {
        return $this->status->isImmutable();
    }

    public function displayName(): string
    {
        $preferred = $this->preferred_name !== null && trim($this->preferred_name) !== ''
            ? $this->preferred_name.' '.$this->last_name
            : $this->full_name;

        return $preferred;
    }

    public function hasStudents(): bool
    {
        return $this->relationships()->exists();
    }
}
