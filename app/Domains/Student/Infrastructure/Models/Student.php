<?php

declare(strict_types=1);

namespace App\Domains\Student\Infrastructure\Models;

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Infrastructure\Models\ClassSection;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\School\Models\School;
use App\Domains\Student\Domain\Enums\Gender;
use App\Domains\Student\Domain\Enums\StudentStatus;
use App\Domains\Student\Domain\Enums\TransferStatus;
use App\Models\User;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Aggregate root of the Student domain (SPR-003).
 *
 * Invariants are enforced by the application actions, never by the model
 * itself: a unique student number per school, an optional but unique email,
 * a non-future birth date, an admission date inside the assigned academic
 * year, and a single current academic assignment.
 */
class Student extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'school_id',
        'student_number',
        'admission_number',
        'national_id',
        'birth_certificate_reference',
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
        'address_street',
        'address_city',
        'address_state',
        'address_postal_code',
        'address_country',
        'admission_date',
        'academic_calendar_year_id',
        'school_class_id',
        'class_section_id',
        'allergies',
        'medical_notes',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'user_id',
        'notes',
        'status',
        'transfer_status',
        'transferred_at',
        'transfer_from_school',
        'transfer_to_school',
        'graduated_at',
        'suspended_at',
        'suspended_until',
        'suspension_reason',
        'withdrawn_at',
        'withdrawal_reason',
        'reactivated_at',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => StudentStatus::class,
            'transfer_status' => TransferStatus::class,
            'gender' => Gender::class,
            'date_of_birth' => 'date',
            'admission_date' => 'date',
            'graduated_at' => 'date',
            'suspended_until' => 'date',
            'transferred_at' => 'datetime',
            'suspended_at' => 'datetime',
            'withdrawn_at' => 'datetime',
            'reactivated_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Student\StudentFactory();
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

    public function academicCalendarYear(): BelongsTo
    {
        return $this->belongsTo(AcademicCalendarYear::class, 'academic_calendar_year_id');
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(ClassSection::class, 'class_section_id');
    }

    public function guardians(): HasMany
    {
        return $this->hasMany(StudentGuardian::class, 'student_id');
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

    public function scopeByStatus($query, StudentStatus|string $status)
    {
        return $query->where('status', $status instanceof StudentStatus ? $status->value : $status);
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

    public function isWithdrawn(): bool
    {
        return $this->status === StudentStatus::Withdrawn;
    }

    public function isInactive(): bool
    {
        return $this->status === StudentStatus::Inactive;
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
}
