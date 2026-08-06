<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Models;

use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;
use App\Domains\AcademicCommunication\Domain\Enums\PublicationStatus;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * One delivery attempt of a document through one channel to one recipient
 * (SPR-012). Records are immutable once terminal; re-delivery creates a new
 * record so the full delivery history is preserved and auditable.
 */
class PublicationRecord extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'publication_records';

    protected $fillable = [
        'uuid',
        'school_id',
        'academic_document_id',
        'document_version_id',
        'channel',
        'recipient_type',
        'recipient_id',
        'recipient_label',
        'visibility_scope',
        'status',
        'attempts',
        'last_error',
        'published_at',
        'cancelled_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'channel' => DocumentChannel::class,
            'status' => PublicationStatus::class,
            'attempts' => 'integer',
            'published_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\AcademicCommunication\PublicationRecordFactory();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function document(): BelongsTo
    {
        return $this->belongsTo(AcademicDocument::class, 'academic_document_id');
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(AcademicDocumentVersion::class, 'document_version_id');
    }

    public function deliveryAttempts(): HasMany
    {
        return $this->hasMany(PublicationDeliveryAttempt::class, 'publication_record_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isCompleted(): bool
    {
        return $this->status === PublicationStatus::Completed;
    }

    public function isTerminal(): bool
    {
        return $this->status->isTerminal();
    }
}
