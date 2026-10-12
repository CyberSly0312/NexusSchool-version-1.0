<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Infrastructure\Models;

use App\Domains\CertificateAndTranscript\Domain\Enums\CertificateEligibilityVerdict;
use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionDocumentKind;
use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionRequestStatus;
use App\Domains\CertificateAndTranscript\Domain\Exceptions\InvalidStatusTransitionException;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Aggregate root of the Certificate & Transcript engine (SPR-015).
 *
 * A completion request covers one student for one completion document (a
 * transcript or an end-of-study certificate). Invariants are enforced by the
 * application actions, never by the model itself: a forward-only lifecycle
 * (Draft → Generated → Published → Archived). The eligibility verdict and its
 * evidence citations are persisted on the request for auditability.
 *
 * @property CompletionDocumentKind $completion_document_kind
 * @property CompletionRequestStatus $status
 * @property CertificateEligibilityVerdict|null $eligibility_verdict
 * @property array<int, string>|null $eligibility_reasons
 * @property array<int, string>|null $evidence_citations
 * @property \Illuminate\Support\Carbon|null $generated_at
 * @property \Illuminate\Support\Carbon|null $reviewed_at
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $archived_at
 */
class CompletionRequest extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'completion_requests';

    protected $fillable = [
        'uuid',
        'school_id',
        'student_id',
        'completion_document_kind',
        'status',
        'eligibility_verdict',
        'eligibility_reasons',
        'evidence_citations',
        'created_by',
        'generated_at',
        'reviewed_at',
        'approved_at',
        'published_at',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'completion_document_kind' => CompletionDocumentKind::class,
            'status' => CompletionRequestStatus::class,
            'eligibility_verdict' => CertificateEligibilityVerdict::class,
            'eligibility_reasons' => 'array',
            'evidence_citations' => 'array',
            'generated_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'approved_at' => 'datetime',
            'published_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\CertificateAndTranscript\CompletionRequestFactory();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * @return HasMany<CompletionRequestEntry, $this>
     */
    public function entries(): HasMany
    {
        return $this->hasMany(CompletionRequestEntry::class, 'completion_request_id');
    }

    /**
     * @return BelongsTo<\App\Domains\Student\Infrastructure\Models\Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Student\Infrastructure\Models\Student::class, 'student_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Lifecycle helpers
    |--------------------------------------------------------------------------
    */

    public function isDraft(): bool
    {
        return $this->status === CompletionRequestStatus::Draft;
    }

    public function isGenerated(): bool
    {
        return $this->status === CompletionRequestStatus::Generated;
    }

    public function isPublished(): bool
    {
        return $this->status === CompletionRequestStatus::Published;
    }

    public function isArchived(): bool
    {
        return $this->status === CompletionRequestStatus::Archived;
    }

    public function isImmutable(): bool
    {
        return $this->status->isImmutable();
    }

    public function canTransitionTo(CompletionRequestStatus $to): bool
    {
        return $to->allowsTransitionFrom($this->status);
    }

    /**
     * @throws InvalidStatusTransitionException
     */
    public function assertCanTransitionTo(CompletionRequestStatus $to): void
    {
        if (! $this->canTransitionTo($to)) {
            throw InvalidStatusTransitionException::from($this->status, $to);
        }
    }

    public function displayName(): string
    {
        return sprintf('%s — %s', $this->completion_document_kind->label(), $this->status->value);
    }
}
