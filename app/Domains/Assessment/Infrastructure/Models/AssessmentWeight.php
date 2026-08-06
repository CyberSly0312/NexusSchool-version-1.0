<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Infrastructure\Models;

use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A weight assigned to an assessment type, optionally scoped to a subject and
 * an academic period (SPR-009). The sum of weights per scope must respect the
 * school ceiling policy.
 */
class AssessmentWeight extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'assessment_weights';

    protected $fillable = [
        'uuid',
        'school_id',
        'assessment_type_id',
        'academic_subject_id',
        'academic_calendar_year_id',
        'academic_calendar_term_id',
        'weight',
        'is_default',
        'is_active',
        'effective_from',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'float',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'effective_from' => 'date',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Assessment\AssessmentWeightFactory();
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(AssessmentType::class, 'assessment_type_id');
    }
}
