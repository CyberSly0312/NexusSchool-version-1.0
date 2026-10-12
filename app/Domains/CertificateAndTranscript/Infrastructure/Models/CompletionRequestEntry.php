<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Infrastructure\Models;

use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionDocumentKind;
use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionRequestEntryStatus;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * One completion document of a request — the immutable snapshot (SPR-015).
 *
 * The content column holds the snapshot composed by the engine from the
 * published evidence (report-card snapshots, council decision, graduation
 * facts, policies). The academic_document_id points at the official document
 * owned by the Academic Communication engine.
 *
 * @property array<string, mixed> $content
 * @property CompletionDocumentKind|null $academic_document_type
 * @property int|null $academic_document_id
 * @property string|null $academic_document_number
 * @property CompletionRequestEntryStatus $status
 */
class CompletionRequestEntry extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'completion_request_entries';

    protected $fillable = [
        'uuid',
        'school_id',
        'completion_request_id',
        'content',
        'academic_document_id',
        'academic_document_number',
        'academic_document_type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'academic_document_type' => CompletionDocumentKind::class,
            'status' => CompletionRequestEntryStatus::class,
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\CertificateAndTranscript\CompletionRequestEntryFactory();
    }

    /**
     * @return BelongsTo<CompletionRequest, $this>
     */
    public function completionRequest(): BelongsTo
    {
        return $this->belongsTo(CompletionRequest::class, 'completion_request_id');
    }

    /**
     * @return BelongsTo<\App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument, $this>
     */
    public function academicDocument(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument::class, 'academic_document_id');
    }
}
