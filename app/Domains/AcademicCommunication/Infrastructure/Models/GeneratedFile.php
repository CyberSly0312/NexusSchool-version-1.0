<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Models;

use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A rendered file produced for a document version (SPR-012). The engine renders
 * every document through the renderer; the official immutable file lives here.
 * Only one file per version is current at a time — re-rendering marks the
 * previous file historical instead of overwriting it.
 */
class GeneratedFile extends Model
{
    use HasFactory;
    use HasSchoolId;

    protected $table = 'generated_files';

    protected $fillable = [
        'uuid',
        'school_id',
        'academic_document_id',
        'document_version_id',
        'file_path',
        'file_name',
        'disk',
        'mime_type',
        'size_bytes',
        'checksum',
        'is_current',
    ];

    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
            'is_current' => 'boolean',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\AcademicCommunication\GeneratedFileFactory();
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(AcademicDocument::class, 'academic_document_id');
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(AcademicDocumentVersion::class, 'document_version_id');
    }
}
