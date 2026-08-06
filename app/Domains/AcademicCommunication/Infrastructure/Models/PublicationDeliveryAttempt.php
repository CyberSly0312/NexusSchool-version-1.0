<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Models;

use App\Domains\AcademicCommunication\Domain\Enums\PublicationStatus;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One attempt of a PublicationRecord delivery (SPR-012). Append-only: every
 * retry (queued rendering, retried channel push) is recorded, so the delivery
 * history stays complete and auditable.
 */
class PublicationDeliveryAttempt extends Model
{
    use HasFactory;
    use HasSchoolId;

    protected $table = 'publication_delivery_attempts';

    protected $fillable = [
        'uuid',
        'school_id',
        'publication_record_id',
        'status',
        'error',
        'attempted_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => PublicationStatus::class,
            'attempted_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function record(): BelongsTo
    {
        return $this->belongsTo(PublicationRecord::class, 'publication_record_id');
    }
}
