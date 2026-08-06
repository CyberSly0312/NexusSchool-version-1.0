<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Models;

use App\Domains\AcademicCommunication\Domain\Enums\SignatureType;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A signature applied to a document version (SPR-012). Supports manual,
 * electronic, digital and QR-code signatures; future digital-signature
 * providers attach through the SignatureType enum without schema changes.
 */
class DocumentSignature extends Model
{
    use HasFactory;
    use HasSchoolId;

    protected $table = 'document_signatures';

    protected $fillable = [
        'uuid',
        'school_id',
        'academic_document_id',
        'document_version_id',
        'signature_type',
        'role',
        'signatory_name',
        'signatory_user_id',
        'signed_at',
        'signature_value',
    ];

    protected function casts(): array
    {
        return [
            'signature_type' => SignatureType::class,
            'signed_at' => 'datetime',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\AcademicCommunication\DocumentSignatureFactory();
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
