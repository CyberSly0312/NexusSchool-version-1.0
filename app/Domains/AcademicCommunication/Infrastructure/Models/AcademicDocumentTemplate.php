<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Models;

use App\Domains\AcademicCommunication\Domain\Enums\DocumentType;
use App\Domains\AcademicCommunication\Domain\ValueObjects\SignatureArea;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Reusable document template of the Academic Communication Engine (SPR-012).
 *
 * A template carries the header/footer/branding (school logo, typography,
 * signature areas, QR-code and verification-code placeholders) and the body
 * content with token placeholders. Templates are locale-aware and belong to a
 * school; the engine picks the active default template for a document type.
 */
class AcademicDocumentTemplate extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'academic_document_templates';

    protected $fillable = [
        'uuid',
        'school_id',
        'name',
        'code',
        'document_type',
        'content',
        'header_html',
        'footer_html',
        'branding',
        'typography',
        'signature_areas',
        'qr_code_enabled',
        'verification_code_enabled',
        'locale',
        'is_active',
        'is_default',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'document_type' => DocumentType::class,
            'content' => 'array',
            'branding' => 'array',
            'typography' => 'array',
            'signature_areas' => 'array',
            'qr_code_enabled' => 'boolean',
            'verification_code_enabled' => 'boolean',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'settings' => 'array',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\AcademicCommunication\AcademicDocumentTemplateFactory();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function documents(): HasMany
    {
        return $this->hasMany(AcademicDocument::class, 'academic_document_template_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * @return list<SignatureArea>
     */
    public function signatureAreas(): array
    {
        return array_map(
            static fn (array $area): SignatureArea => SignatureArea::fromArray($area),
            $this->signature_areas ?? [],
        );
    }

    public function displayName(): string
    {
        return sprintf('%s [%s]', $this->name, $this->document_type->label());
    }
}
