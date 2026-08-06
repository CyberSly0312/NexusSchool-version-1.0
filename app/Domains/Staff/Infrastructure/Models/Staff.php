<?php

declare(strict_types=1);

namespace App\Domains\Staff\Infrastructure\Models;

use App\Domains\School\Models\School;
use App\Domains\Staff\Domain\Enums\StaffSpecialization;
use App\Domains\Staff\Domain\Enums\StaffStatus;
use App\Models\User;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Aggregate root of the Staff domain (SPR-006).
 *
 * Teacher, Director, Secretary, Accountant, Librarian and other employees
 * are specializations of a single Staff record — never duplicated aggregate
 * roots. The professional identity lives here; employment, assignments,
 * qualifications and certifications are supporting entities. Invariants are
 * enforced by the application actions, never by the model.
 */
class Staff extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'staffs';

    protected $fillable = [
        'uuid',
        'school_id',
        'staff_number',
        'first_name',
        'last_name',
        'middle_name',
        'preferred_name',
        'gender',
        'date_of_birth',
        'nationality',
        'photo',
        'email',
        'phone_number',
        'alternate_phone',
        'residential_address',
        'national_id',
        'tax_number',
        'bank_name',
        'bank_account',
        'specialization',
        'user_id',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => StaffStatus::class,
            'specialization' => StaffSpecialization::class,
            'date_of_birth' => 'date',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Staff\StaffFactory();
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

    public function employment(): HasOne
    {
        return $this->hasOne(Employment::class, 'staff_id')
            ->whereNull('termination_date')
            ->latestOfMany();
    }

    public function employments(): HasMany
    {
        return $this->hasMany(Employment::class, 'staff_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'staff_id');
    }

    public function qualifications(): HasMany
    {
        return $this->hasMany(Qualification::class, 'staff_id');
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class, 'staff_id');
    }

    public function emergencyContacts(): HasMany
    {
        return $this->hasMany(StaffEmergencyContact::class, 'staff_id');
    }

    public function teacherProfile(): HasOne
    {
        return $this->hasOne(TeacherProfile::class, 'staff_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', StaffStatus::Active);
    }

    public function scopeByStatus($query, StaffStatus|string $status)
    {
        return $query->where('status', $status instanceof StaffStatus ? $status->value : $status);
    }

    public function scopeBySpecialization($query, StaffSpecialization|string $specialization)
    {
        return $query->where(
            'specialization',
            $specialization instanceof StaffSpecialization ? $specialization->value : $specialization,
        );
    }

    public function scopeTeachers($query)
    {
        return $query->where('specialization', StaffSpecialization::Teacher);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === StaffStatus::Active;
    }

    public function isSuspended(): bool
    {
        return $this->status === StaffStatus::Suspended;
    }

    public function isImmutable(): bool
    {
        return $this->status->isImmutable();
    }

    public function isTeacher(): bool
    {
        return $this->specialization === StaffSpecialization::Teacher;
    }

    public function displayName(): string
    {
        $preferred = $this->preferred_name !== null && trim($this->preferred_name) !== ''
            ? $this->preferred_name.' '.$this->last_name
            : $this->full_name;

        return $preferred;
    }

    public function hasActiveEmployment(): bool
    {
        return $this->employment()->exists();
    }
}
