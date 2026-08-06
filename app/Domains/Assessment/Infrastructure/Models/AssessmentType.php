<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Infrastructure\Models;

use App\Domains\Assessment\Domain\Enums\AssessmentType as AssessmentTypeEnum;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A school-level category of assessments backed by the configurable
 * AssessmentType enum (SPR-009). Custom types are the schools' own extension
 * points: they map to the `custom` enum case.
 */
class AssessmentType extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'assessment_types';

    protected $fillable = [
        'uuid',
        'school_id',
        'name',
        'code',
        'type_key',
        'description',
        'is_active',
        'sort_order',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'type_key' => AssessmentTypeEnum::class,
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Assessment\AssessmentTypeFactory();
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'assessment_type_id');
    }

    public function isCustom(): bool
    {
        return $this->type_key?->isCustom() ?? false;
    }
}
