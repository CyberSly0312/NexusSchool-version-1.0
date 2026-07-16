<?php

namespace App\Models;

use App\Domains\School\Models\School;
use App\Domains\StudentEnrollment\Enums\EnrollmentStatus;
use App\Domains\StudentEnrollment\Enums\EnrollmentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class StudentEnrollment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'school_id',
        'student_id',
        'academic_year_id',
        'academic_class_id',
        'program_id',
        'grade_level_id',
        'section_id',
        'classroom_id',
        'academic_term_id',
        'enrollment_number',
        'enrollment_date',
        'effective_date',
        'enrollment_type',
        'status',
        'previous_school',
        'transfer_reason',
        'exit_date',
        'exit_reason',
        'notes',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (StudentEnrollment $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'enrollment_date' => 'date',
            'effective_date' => 'date',
            'exit_date' => 'date',
            'status' => EnrollmentStatus::class,
            'enrollment_type' => EnrollmentType::class,
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function academicClass(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', EnrollmentStatus::Active);
    }

    public function scopePending($query)
    {
        return $query->where('status', EnrollmentStatus::Pending);
    }

    public function scopeForSchool($query, string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForAcademicYear($query, int $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    public function isActive(): bool
    {
        return $this->status === EnrollmentStatus::Active;
    }

    public function isPending(): bool
    {
        return $this->status === EnrollmentStatus::Pending;
    }

    public function isSuspended(): bool
    {
        return $this->status === EnrollmentStatus::Suspended;
    }

    public function isCompleted(): bool
    {
        return $this->status === EnrollmentStatus::Completed;
    }

    public function isCancelled(): bool
    {
        return $this->status === EnrollmentStatus::Cancelled;
    }

    public function isWithdrawn(): bool
    {
        return $this->status === EnrollmentStatus::Withdrawn;
    }

    public function canTransitionTo(EnrollmentStatus $target): bool
    {
        return $this->status->canTransitionTo($target);
    }
}
