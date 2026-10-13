<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Models;

use App\Domains\AcademicCommunication\Domain\Enums\ApprovalStatus;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentOrigin;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentStatus;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentType;
use App\Domains\AcademicCommunication\Domain\Exceptions\DocumentImmutableException;
use App\Domains\AcademicCommunication\Domain\Exceptions\InvalidStatusTransitionException;
use App\Domains\AcademicCommunication\Domain\ValueObjects\DocumentNumber;
use App\Domains\AcademicCommunication\Domain\ValueObjects\VisibilityPolicy;
use App\Domains\IdentityAndVerification\Infrastructure\Models\VerificationIdentity;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Aggregate root of the Academic Communication Engine (SPR-012).
 *
 * An AcademicDocument is a permanent, identity-carrying official communication.
 * Its document_number is assigned once and never changes. Published versions
 * are immutable: any correction produces a new version, never an overwrite.
 * Invariants (forward-only lifecycle, immutability of published versions,
 * immutable identity) are enforced by the application actions and by the model
 * helpers below — never bypassed by the presentation layer.
 */
class AcademicDocument extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'academic_documents';

    protected $fillable = [
        'uuid',
        'school_id',
        'document_number',
        'document_type',
        'title',
        'status',
        'approval_status',
        'approval_required',
        'origin',
        'academic_calendar_year_id',
        'academic_calendar_term_id',
        'school_class_id',
        'class_section_id',
        'student_id',
        'staff_id',
        'council_session_id',
        'academic_document_template_id',
        'publication_policy_id',
        'content',
        'content_version',
        'visibility_policy',
        'generated_by',
        'generated_at',
        'reviewed_at',
        'published_at',
        'archived_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'document_type' => DocumentType::class,
            'status' => DocumentStatus::class,
            'approval_status' => ApprovalStatus::class,
            'origin' => DocumentOrigin::class,
            'approval_required' => 'boolean',
            'content' => 'array',
            'visibility_policy' => 'array',
            'metadata' => 'array',
            'generated_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'published_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\AcademicCommunication\AcademicDocumentFactory();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function template(): BelongsTo
    {
        return $this->belongsTo(AcademicDocumentTemplate::class, 'academic_document_template_id');
    }

    public function publicationPolicy(): BelongsTo
    {
        return $this->belongsTo(PublicationPolicy::class, 'publication_policy_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(AcademicDocumentVersion::class, 'academic_document_id');
    }

    public function currentVersion(): HasOne
    {
        return $this->hasOne(AcademicDocumentVersion::class, 'academic_document_id')
            ->orderByDesc('version_number');
    }

    public function publicationRecords(): HasMany
    {
        return $this->hasMany(PublicationRecord::class, 'academic_document_id');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(DocumentRecipient::class, 'academic_document_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(DocumentApproval::class, 'academic_document_id')
            ->where('document_approvals.school_id', $this->school_id);
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(DocumentSignature::class, 'academic_document_id');
    }

    public function audits(): HasMany
    {
        return $this->hasMany(DocumentAudit::class, 'academic_document_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(GeneratedFile::class, 'academic_document_id');
    }

    /**
     * Read-only verification identity issued for this document (SPR-016).
     *
     * At most one identity exists per document (unique academic_document_id).
     * Only non-sensitive metadata (status, lifecycle timestamps) is persisted;
     * the opaque token digest is never surfaced by the presentation layer.
     */
    public function verificationIdentity(): HasOne
    {
        return $this->hasOne(VerificationIdentity::class, 'academic_document_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Identity
    |--------------------------------------------------------------------------
    */

    public function documentNumber(): DocumentNumber
    {
        return DocumentNumber::fromString((string) $this->document_number);
    }

    /*
    |--------------------------------------------------------------------------
    | Lifecycle helpers
    |--------------------------------------------------------------------------
    */

    public function isDraft(): bool
    {
        return $this->status === DocumentStatus::Draft;
    }

    public function isGenerated(): bool
    {
        return $this->status === DocumentStatus::Generated;
    }

    public function isReviewed(): bool
    {
        return $this->status === DocumentStatus::Reviewed;
    }

    public function isApproved(): bool
    {
        return $this->status === DocumentStatus::Approved;
    }

    public function isPublished(): bool
    {
        return $this->status === DocumentStatus::Published;
    }

    public function isArchived(): bool
    {
        return $this->status === DocumentStatus::Archived;
    }

    public function isImmutable(): bool
    {
        return $this->status->isImmutable();
    }

    public function requiresApproval(): bool
    {
        return $this->approval_required;
    }

    public function isApprovalPending(): bool
    {
        return $this->approval_status === ApprovalStatus::Pending;
    }

    public function canTransitionTo(DocumentStatus $to): bool
    {
        return $to->allowsTransitionFrom($this->status);
    }

    /**
     * @throws InvalidStatusTransitionException
     */
    public function assertCanTransitionTo(DocumentStatus $to): void
    {
        if (! $this->canTransitionTo($to)) {
            throw InvalidStatusTransitionException::from($this->status, $to);
        }
    }

    /**
     * @throws DocumentImmutableException
     */
    public function assertMutable(): void
    {
        if ($this->isImmutable()) {
            throw DocumentImmutableException::forDocument($this->getKey(), $this->status->value);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Content / visibility
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<string, mixed>
     */
    public function content(): array
    {
        return $this->content ?? [];
    }

    public function visibilityPolicy(): VisibilityPolicy
    {
        return VisibilityPolicy::fromArray($this->visibility_policy ?? []);
    }

    public function displayName(): string
    {
        return sprintf('%s — %s', $this->title, $this->document_type->label());
    }
}
