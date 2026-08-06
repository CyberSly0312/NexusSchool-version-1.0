<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Models;

use App\Domains\AcademicCommunication\Domain\Enums\DocumentStatus;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * One immutable snapshot of an AcademicDocument (SPR-012).
 *
 * A version captures the exact rendered content at a point in time. Once the
 * document reaches Published the active version becomes immutable: its
 * content_hash prevents any silent mutation, and corrections always create a
 * new version under the same document identity.
 */
class AcademicDocumentVersion extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'academic_document_versions';

    protected $fillable = [
        'uuid',
        'school_id',
        'academic_document_id',
        'version_number',
        'status',
        'content',
        'content_hash',
        'is_published',
        'published_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => DocumentStatus::class,
            'content' => 'array',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\AcademicCommunication\AcademicDocumentVersionFactory();
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

    public function publicationRecords(): HasMany
    {
        return $this->hasMany(PublicationRecord::class, 'document_version_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(DocumentApproval::class, 'document_version_id');
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(DocumentSignature::class, 'document_version_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(GeneratedFile::class, 'document_version_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * @param  array<string, mixed>  $content
     */
    public static function hashContent(array $content): string
    {
        return hash('sha256', (string) json_encode($content, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
    }

    public function isPublished(): bool
    {
        return $this->is_published || $this->status === DocumentStatus::Published;
    }
}
