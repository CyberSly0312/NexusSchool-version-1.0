<?php

namespace App\Models;

use App\Domains\Gradebook\Enums\AssessmentResultStatus;
use App\Domains\School\Models\School;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class AssessmentResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'school_id',
        'student_id',
        'student_enrollment_id',
        'assessment_id',
        'subject_id',
        'user_id',
        'academic_year_id',
        'academic_term_id',
        'raw_score',
        'adjusted_score',
        'maximum_score',
        'percentage',
        'letter_grade',
        'grade_point',
        'coefficient',
        'weighted_score',
        'rank_in_assessment',
        'is_absent',
        'is_excused',
        'is_published',
        'is_locked',
        'status',
        'remarks',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (AssessmentResult $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'raw_score' => 'decimal:2',
            'adjusted_score' => 'decimal:2',
            'maximum_score' => 'decimal:2',
            'percentage' => 'decimal:2',
            'grade_point' => 'decimal:2',
            'coefficient' => 'decimal:2',
            'weighted_score' => 'decimal:4',
            'rank_in_assessment' => 'integer',
            'is_absent' => 'boolean',
            'is_excused' => 'boolean',
            'is_published' => 'boolean',
            'is_locked' => 'boolean',
            'status' => AssessmentResultStatus::class,
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

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function studentEnrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->whereIn('status', [AssessmentResultStatus::Entered, AssessmentResultStatus::Verified]);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', AssessmentResultStatus::Draft);
    }

    public function scopeVerified($query)
    {
        return $query->where('status', AssessmentResultStatus::Verified);
    }

    public function scopePublished($query)
    {
        return $query->where('status', AssessmentResultStatus::Published);
    }

    public function scopeForSchool($query, string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForAssessment($query, int $assessmentId)
    {
        return $query->where('assessment_id', $assessmentId);
    }

    public function scopeForSubject($query, int $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeForAcademicYear($query, int $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    public function scopeForAcademicTerm($query, int $academicTermId)
    {
        return $query->where('academic_term_id', $academicTermId);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isDraft(): bool
    {
        return $this->status === AssessmentResultStatus::Draft;
    }

    public function isEntered(): bool
    {
        return $this->status === AssessmentResultStatus::Entered;
    }

    public function isVerified(): bool
    {
        return $this->status === AssessmentResultStatus::Verified;
    }

    public function isPublished(): bool
    {
        return $this->status === AssessmentResultStatus::Published;
    }

    public function isLocked(): bool
    {
        return $this->status === AssessmentResultStatus::Locked;
    }

    public function isReadOnly(): bool
    {
        return $this->isLocked() || $this->isPublished();
    }

    public function canTransitionTo(AssessmentResultStatus $target): bool
    {
        return $this->status->canTransitionTo($target);
    }

    public function calculatePercentage(): float
    {
        if ($this->maximum_score <= 0 || $this->is_absent) {
            return 0;
        }

        $score = $this->adjusted_score ?? $this->raw_score;

        return round(($score / $this->maximum_score) * 100, 2);
    }
}
