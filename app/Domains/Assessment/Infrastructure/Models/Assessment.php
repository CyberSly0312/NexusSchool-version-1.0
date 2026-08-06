<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Infrastructure\Models;

use App\Domains\Assessment\Domain\Enums\AssessmentStatus;
use App\Domains\Assessment\Domain\Exceptions\AssessmentDefinitionImmutableException;
use App\Domains\Assessment\Domain\Exceptions\AssessmentNotScoreableException;
use App\Domains\Assessment\Domain\Exceptions\InvalidStatusTransitionException;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Aggregate root of the Assessment engine (SPR-009).
 *
 * An assessment is a graded activity bound to exactly one subject, one academic
 * period (year + term) and one teacher. Invariants are enforced by the
 * application actions, never by the model itself: a forward-only lifecycle
 * (Draft → Scheduled → Published → Completed → Archived), definition
 * immutability once published, and score immutability once completed.
 */
class Assessment extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'assessments';

    protected $fillable = [
        'uuid',
        'school_id',
        'academic_calendar_year_id',
        'academic_calendar_term_id',
        'academic_subject_id',
        'school_class_id',
        'class_section_id',
        'staff_id',
        'assessment_type_id',
        'assessment_template_id',
        'assessment_session_id',
        'title',
        'description',
        'maximum_score',
        'passing_score',
        'weight',
        'status',
        'scheduled_at',
        'published_at',
        'completed_at',
        'archived_at',
        'created_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => AssessmentStatus::class,
            'maximum_score' => 'float',
            'passing_score' => 'float',
            'weight' => 'float',
            'scheduled_at' => 'datetime',
            'published_at' => 'datetime',
            'completed_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Assessment\AssessmentFactory();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function type(): BelongsTo
    {
        return $this->belongsTo(AssessmentType::class, 'assessment_type_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(AssessmentTemplate::class, 'assessment_template_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AssessmentSession::class, 'assessment_session_id');
    }

    public function components(): HasMany
    {
        return $this->hasMany(AssessmentComponent::class, 'assessment_id')
            ->orderBy('sort_order');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(AssessmentScore::class, 'assessment_id');
    }

    public function remarks(): HasMany
    {
        return $this->hasMany(AssessmentRemark::class, 'assessment_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(AssessmentAttachment::class, 'assessment_id');
    }

    public function statistics(): HasMany
    {
        return $this->hasMany(AssessmentStatistics::class, 'assessment_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Lifecycle helpers
    |--------------------------------------------------------------------------
    */

    public function isDraft(): bool
    {
        return $this->status === AssessmentStatus::Draft;
    }

    public function isScheduled(): bool
    {
        return $this->status === AssessmentStatus::Scheduled;
    }

    public function isPublished(): bool
    {
        return $this->status === AssessmentStatus::Published;
    }

    public function isCompleted(): bool
    {
        return $this->status === AssessmentStatus::Completed;
    }

    public function isArchived(): bool
    {
        return $this->status === AssessmentStatus::Archived;
    }

    public function isDefinitionMutable(): bool
    {
        return ! $this->status->isDefinitionImmutable();
    }

    public function isScoreable(): bool
    {
        return $this->status->isScoreable();
    }

    public function canTransitionTo(AssessmentStatus $to): bool
    {
        return $to->allowsTransitionFrom($this->status);
    }

    /**
     * @throws InvalidStatusTransitionException
     */
    public function assertCanTransitionTo(AssessmentStatus $to): void
    {
        if (! $this->canTransitionTo($to)) {
            throw InvalidStatusTransitionException::from($this->status, $to);
        }
    }

    /**
     * @throws AssessmentDefinitionImmutableException
     */
    public function assertDefinitionMutable(): void
    {
        if (! $this->isDefinitionMutable()) {
            throw AssessmentDefinitionImmutableException::forAssessment($this->getKey(), $this->status->value);
        }
    }

    /**
     * @throws AssessmentNotScoreableException
     */
    public function assertScoreable(): void
    {
        if (! $this->isScoreable()) {
            throw AssessmentNotScoreableException::forAssessment($this->getKey(), $this->status->value);
        }
    }

    public function displayName(): string
    {
        return sprintf('%s — %s', $this->title, $this->status->value);
    }
}
