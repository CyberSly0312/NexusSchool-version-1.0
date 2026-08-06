<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Infrastructure\Models;

use App\Domains\Assessment\Domain\Enums\ScoreStatus;
use App\Domains\Assessment\Domain\Enums\ScoreValidationStatus;
use App\Domains\Assessment\Domain\Exceptions\ScoreNotMutableException;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A single student's score on an assessment (SPR-009).
 *
 * Bound to an enrollment, never to a student directly. One score per enrollment
 * per component per assessment (unique constraint). A Draft score is editable;
 * a Published score is immutable.
 */
class AssessmentScore extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'assessment_scores';

    protected $fillable = [
        'uuid',
        'school_id',
        'assessment_id',
        'assessment_component_id',
        'enrollment_id',
        'score',
        'percentage',
        'status',
        'validation_status',
        'feedback',
        'graded_by',
        'graded_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'float',
            'percentage' => 'float',
            'status' => ScoreStatus::class,
            'validation_status' => ScoreValidationStatus::class,
            'graded_at' => 'datetime',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Assessment\AssessmentScoreFactory();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(AssessmentComponent::class, 'assessment_component_id');
    }

    public function remarks(): HasMany
    {
        return $this->hasMany(AssessmentRemark::class, 'assessment_score_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Lifecycle helpers
    |--------------------------------------------------------------------------
    */

    public function isMutable(): bool
    {
        return $this->status?->isMutable() ?? true;
    }

    /**
     * @throws ScoreNotMutableException
     */
    public function assertMutable(): void
    {
        if (! $this->isMutable()) {
            throw ScoreNotMutableException::forScore($this->getKey());
        }
    }
}
