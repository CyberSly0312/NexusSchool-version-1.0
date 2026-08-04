<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Infrastructure\Models;

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Infrastructure\Models\AcademicProgram;
use App\Domains\Academic\Infrastructure\Models\ClassSection;
use App\Domains\Academic\Infrastructure\Models\EducationLevel;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\Enrollment\Domain\Enums\EnrollmentStatus;
use App\Domains\Enrollment\Domain\Enums\EnrollmentType;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Aggregate root of the Enrollment domain (SPR-004).
 *
 * An Enrollment is a temporal, per-academic-year assignment of a Student to a
 * Class (and optionally a Section). Invariants are enforced by the application
 * actions, never by the model itself: one active enrollment per student per
 * year, an active academic year, and append-only history.
 */
class Enrollment extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'school_id',
        'student_id',
        'academic_calendar_year_id',
        'academic_program_id',
        'education_level_id',
        'school_class_id',
        'class_section_id',
        'enrollment_number',
        'enrollment_type',
        'status',
        'enrolled_at',
        'exited_at',
        'exit_reason',
        'suspension_reason',
        'suspended_until',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'enrollment_type' => EnrollmentType::class,
            'status' => EnrollmentStatus::class,
            'enrolled_at' => 'date',
            'exited_at' => 'date',
            'suspended_until' => 'date',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Enrollment\EnrollmentFactory();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function academicCalendarYear(): BelongsTo
    {
        return $this->belongsTo(AcademicCalendarYear::class, 'academic_calendar_year_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(AcademicProgram::class, 'academic_program_id');
    }

    public function educationLevel(): BelongsTo
    {
        return $this->belongsTo(EducationLevel::class, 'education_level_id');
    }

    /**
     * @return BelongsTo<SchoolClass, $this>
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(ClassSection::class, 'class_section_id');
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(EnrollmentPromotion::class, 'enrollment_id');
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(EnrollmentTransfer::class, 'enrollment_id');
    }

    public function graduations(): HasMany
    {
        return $this->hasMany(EnrollmentGraduation::class, 'enrollment_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(EnrollmentHistory::class, 'enrollment_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', EnrollmentStatus::Active);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', EnrollmentStatus::Archived);
    }

    public function scopeByStatus($query, EnrollmentStatus|string $status)
    {
        return $query->where('status', $status instanceof EnrollmentStatus ? $status->value : $status);
    }

    public function scopeForYear($query, int $academicCalendarYearId)
    {
        return $query->where('academic_calendar_year_id', $academicCalendarYearId);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === EnrollmentStatus::Active;
    }

    public function isSuspended(): bool
    {
        return $this->status === EnrollmentStatus::Suspended;
    }

    public function isGraduated(): bool
    {
        return $this->status === EnrollmentStatus::Graduated;
    }

    public function isWithdrawn(): bool
    {
        return $this->status === EnrollmentStatus::Withdrawn;
    }

    public function isArchived(): bool
    {
        return $this->status === EnrollmentStatus::Archived;
    }

    public function isImmutable(): bool
    {
        return $this->status->isImmutable();
    }

    public function displayName(): string
    {
        return sprintf('%s — %s', $this->student?->displayName() ?? "Student #{$this->student_id}", $this->enrollment_number);
    }
}
