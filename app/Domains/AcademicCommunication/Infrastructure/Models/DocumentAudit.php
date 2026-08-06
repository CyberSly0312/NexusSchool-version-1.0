<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Models;

use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Append-only audit journal of the Academic Communication Engine (SPR-012).
 *
 * Captures everything the global AuditLog does not: portal views, downloads,
 * template used, recipient resolution and per-channel publication. Rows are
 * never updated or deleted — history is complete and immutable.
 */
class DocumentAudit extends Model
{
    use HasFactory;
    use HasSchoolId;

    public $timestamps = false;

    protected $table = 'document_audits';

    protected $fillable = [
        'uuid',
        'school_id',
        'academic_document_id',
        'document_version_id',
        'action',
        'actor_user_id',
        'recipient_id',
        'publication_record_id',
        'channel',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(AcademicDocument::class, 'academic_document_id');
    }
}
