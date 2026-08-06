<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Infrastructure\Models;

use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A reusable assessment blueprint (SPR-009). Templates carry default scores,
 * weights and a JSON component structure so teachers create consistent
 * assessments from one source.
 */
class AssessmentTemplate extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'assessment_templates';

    protected $fillable = [
        'uuid',
        'school_id',
        'name',
        'assessment_type_id',
        'description',
        'default_maximum_score',
        'default_passing_score',
        'default_weight',
        'structure',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'default_maximum_score' => 'float',
            'default_passing_score' => 'float',
            'default_weight' => 'float',
            'structure' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Assessment\AssessmentTemplateFactory();
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(AssessmentType::class, 'assessment_type_id');
    }

    public function assessments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Assessment::class, 'assessment_template_id');
    }
}
