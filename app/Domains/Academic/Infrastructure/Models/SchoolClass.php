<?php

declare(strict_types=1);

namespace App\Domains\Academic\Infrastructure\Models;

use App\Domains\Academic\Domain\Enums\EntityStatus;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A concrete class (grade group) inside a program, e.g. "6ème" in the
 * "Collège" program. May belong to an optional stream and own several sections.
 */
class SchoolClass extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'academic_program_id',
        'education_level_id',
        'stream_id',
        'name',
        'code',
        'capacity',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => EntityStatus::class,
            'capacity' => 'integer',
        ];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(AcademicProgram::class, 'academic_program_id');
    }

    public function educationLevel(): BelongsTo
    {
        return $this->belongsTo(EducationLevel::class, 'education_level_id');
    }

    public function stream(): BelongsTo
    {
        return $this->belongsTo(Stream::class, 'stream_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(ClassSection::class, 'school_class_id');
    }

    public function coefficients(): HasMany
    {
        return $this->hasMany(Coefficient::class, 'school_class_id');
    }
}
