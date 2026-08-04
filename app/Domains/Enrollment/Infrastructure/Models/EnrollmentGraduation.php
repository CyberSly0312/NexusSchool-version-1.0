<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Infrastructure\Models;

use App\Domains\Academic\Infrastructure\Models\EducationLevel;
use App\Domains\Enrollment\Domain\Enums\GraduationDecision;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Graduation record closing an enrollment. Immutable history.
 */
class EnrollmentGraduation extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'school_id',
        'enrollment_id',
        'student_id',
        'education_level_id',
        'decision',
        'graduated_on',
        'certificate_reference',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'decision' => GraduationDecision::class,
            'graduated_on' => 'date',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Enrollment\EnrollmentGraduationFactory();
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function educationLevel(): BelongsTo
    {
        return $this->belongsTo(EducationLevel::class, 'education_level_id');
    }
}
