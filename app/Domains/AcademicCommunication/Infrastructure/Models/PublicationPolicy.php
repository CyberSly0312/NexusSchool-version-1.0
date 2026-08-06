<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Models;

use App\Domains\AcademicCommunication\Domain\Enums\DocumentType;
use App\Domains\AcademicCommunication\Domain\ValueObjects\VisibilityPolicy;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Publication policy of the Academic Communication Engine (SPR-012).
 *
 * Directors decide — through a policy — who receives which document, when,
 * through which channel, and whether publication requires approval. Policies
 * are per document type and carry the recipient scopes, the enabled channels,
 * the approval roles and the visibility map applied at generation time.
 */
class PublicationPolicy extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'publication_policies';

    protected $fillable = [
        'uuid',
        'school_id',
        'name',
        'code',
        'document_type',
        'recipient_scopes',
        'channels',
        'requires_approval',
        'approval_roles',
        'visibility',
        'is_active',
        'is_default',
        'effective_from',
        'effective_until',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'document_type' => DocumentType::class,
            'recipient_scopes' => 'array',
            'channels' => 'array',
            'requires_approval' => 'boolean',
            'approval_roles' => 'array',
            'visibility' => 'array',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'effective_from' => 'date',
            'effective_until' => 'date',
            'settings' => 'array',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\AcademicCommunication\PublicationPolicyFactory();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function documents(): HasMany
    {
        return $this->hasMany(AcademicDocument::class, 'publication_policy_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function visibilityPolicy(): VisibilityPolicy
    {
        return VisibilityPolicy::fromArray($this->visibility ?? []);
    }

    public function isCurrentlyEffective(\DateTimeInterface|string|null $at = null): bool
    {
        $moment = $at !== null ? \Illuminate\Support\Carbon::parse($at) : now();

        if ($this->effective_from !== null && $moment->lt($this->effective_from)) {
            return false;
        }

        if ($this->effective_until !== null && $moment->gt($this->effective_until)) {
            return false;
        }

        return true;
    }

    public function displayName(): string
    {
        return sprintf('%s [%s]', $this->name, $this->document_type->label());
    }
}
