<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Infrastructure\Models;

use App\Domains\Guardian\Domain\Enums\RelationshipCategory;
use App\Domains\School\Models\School;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A configurable relationship type (father, mother, guardian, sponsor, …).
 * Rows are materialized per school from config/nexus-guardian.php, but a
 * school can add or edit its own catalog at runtime.
 */
class RelationshipType extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'relationship_types';

    protected $fillable = [
        'uuid',
        'school_id',
        'code',
        'label',
        'description',
        'category',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'category' => RelationshipCategory::class,
            'is_active' => 'boolean',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Guardian\RelationshipTypeFactory();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function relationships(): HasMany
    {
        return $this->hasMany(StudentGuardianRelationship::class, 'relationship_type_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->is_active;
    }
}
