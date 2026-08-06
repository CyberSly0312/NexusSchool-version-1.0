<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Models;

use App\Domains\AcademicCommunication\Domain\Enums\VisibilityScope;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A recipient granted access to a document (SPR-012). Recipients are scoped
 * (director, secretary, teacher, parent, student) and self-scoped recipients
 * (parents, students) are bound to a concrete person so they can only ever see
 * their own data. Portal views and downloads are tracked on this entity.
 */
class DocumentRecipient extends Model
{
    use HasFactory;
    use HasSchoolId;

    protected $table = 'document_recipients';

    protected $fillable = [
        'uuid',
        'school_id',
        'academic_document_id',
        'document_version_id',
        'recipient_type',
        'recipient_id',
        'recipient_label',
        'visibility_scope',
        'portal_viewed_at',
        'portal_view_count',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'visibility_scope' => VisibilityScope::class,
            'portal_viewed_at' => 'datetime',
            'portal_view_count' => 'integer',
            'delivered_at' => 'datetime',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\AcademicCommunication\DocumentRecipientFactory();
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(AcademicDocument::class, 'academic_document_id');
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(AcademicDocumentVersion::class, 'document_version_id');
    }

    /**
     * Mark a portal view (audited by the engine).
     */
    public function markViewed(): void
    {
        $this->portal_view_count = ($this->portal_view_count ?? 0) + 1;
        $this->portal_viewed_at = now();

        $this->save();
    }
}
