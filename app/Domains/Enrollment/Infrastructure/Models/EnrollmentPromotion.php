<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Infrastructure\Models;

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\Enrollment\Domain\Enums\PromotionDecision;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * End-of-year promotion / repetition / class-skip record. Immutable history:
 * the row is created once and never modified.
 */
class EnrollmentPromotion extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'school_id',
        'enrollment_id',
        'student_id',
        'academic_calendar_year_id',
        'from_class_id',
        'to_class_id',
        'decision',
        'promoted_on',
        'academic_result',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'decision' => PromotionDecision::class,
            'promoted_on' => 'date',
            'academic_result' => 'array',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Enrollment\EnrollmentPromotionFactory();
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function academicCalendarYear(): BelongsTo
    {
        return $this->belongsTo(AcademicCalendarYear::class, 'academic_calendar_year_id');
    }

    public function fromClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'from_class_id');
    }

    public function toClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'to_class_id');
    }
}
