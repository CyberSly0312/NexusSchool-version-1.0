<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Infrastructure\Models;

use App\Domains\Gradebook\Domain\Enums\GradeEntryStatus;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A single gradebook line: one student's term grade in one subject (SPR-010).
 *
 * Bound to the enrollment that placed the student in the class. The line
 * stores the canonical percentage and every derived representation (average on
 * the school scale, letter grade, grade point, appreciation, rank) so report
 * cards never recompute and never diverge.
 */
class GradeEntry extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'grade_entries';

    protected $fillable = [
        'uuid',
        'school_id',
        'gradebook_id',
        'enrollment_id',
        'student_id',
        'percentage',
        'average',
        'letter_grade',
        'grade_point',
        'appreciation',
        'rank',
        'rank_denominator',
        'status',
        'remarks',
        'graded_by',
        'calculated_at',
    ];

    protected function casts(): array
    {
        return [
            'percentage' => 'float',
            'average' => 'float',
            'grade_point' => 'float',
            'rank' => 'integer',
            'rank_denominator' => 'integer',
            'status' => GradeEntryStatus::class,
            'calculated_at' => 'datetime',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Gradebook\GradeEntryFactory();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function gradebook(): BelongsTo
    {
        return $this->belongsTo(Gradebook::class, 'gradebook_id');
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Enrollment\Infrastructure\Models\Enrollment::class, 'enrollment_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id');
    }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'graded_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Lifecycle helpers
    |--------------------------------------------------------------------------
    */

    public function isEditable(): bool
    {
        return $this->status?->isEditable() ?? true;
    }

    public function isPassing(float $threshold = 50.0): bool
    {
        return $this->percentage !== null && $this->percentage >= $threshold;
    }
}
