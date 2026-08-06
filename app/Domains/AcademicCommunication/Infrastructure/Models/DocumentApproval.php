<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Models;

use App\Domains\AcademicCommunication\Domain\Enums\ApprovalStatus;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * One approval decision on an AcademicDocument (SPR-012). Approval may be
 * required from several roles (Principal, Secretary, Council President); each
 * role contributes its own DocumentApproval row. Decisions are append-only.
 */
class DocumentApproval extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'document_approvals';

    protected $fillable = [
        'uuid',
        'school_id',
        'academic_document_id',
        'document_version_id',
        'approver_role',
        'approver_user_id',
        'status',
        'comment',
        'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ApprovalStatus::class,
            'decided_at' => 'datetime',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\AcademicCommunication\DocumentApprovalFactory();
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
