<?php

declare(strict_types=1);

namespace App\Domains\Staff\Infrastructure\Models;

use App\Domains\Staff\Domain\Enums\PositionCategory;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Position in the school's workforce. Rows are materialized per tenant from
 * the config-driven catalog (see PositionSeeder) but a school may customize
 * them at runtime.
 */
class Position extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'positions';

    protected $fillable = [
        'uuid',
        'school_id',
        'code',
        'title',
        'description',
        'category',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'category' => PositionCategory::class,
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Staff\PositionFactory();
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\School\Models\School::class, 'school_id');
    }

    public function employments(): HasMany
    {
        return $this->hasMany(Employment::class, 'position_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }
}
