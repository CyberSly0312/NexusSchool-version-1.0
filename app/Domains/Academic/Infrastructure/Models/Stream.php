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
 * An academic stream (e.g. "Sciences", "Lettres", "Technique") within a program.
 */
class Stream extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'academic_program_id',
        'education_level_id',
        'name',
        'code',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => EntityStatus::class,
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

    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'stream_id');
    }
}
