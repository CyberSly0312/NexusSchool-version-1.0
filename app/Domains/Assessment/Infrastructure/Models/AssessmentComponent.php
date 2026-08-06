<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Infrastructure\Models;

use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * One part of an assessment (SPR-009). A project can be split into a written
 * part and an oral part, each with its own maximum score; a score is recorded
 * per component and optionally aggregated at the assessment level.
 */
class AssessmentComponent extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'assessment_components';

    protected $fillable = [
        'uuid',
        'school_id',
        'assessment_id',
        'name',
        'description',
        'maximum_score',
        'weight',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'maximum_score' => 'float',
            'weight' => 'float',
            'sort_order' => 'integer',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Assessment\AssessmentComponentFactory();
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(AssessmentScore::class, 'assessment_component_id');
    }
}
