<?php

declare(strict_types=1);

namespace App\Domains\Academic\Infrastructure\Models;

use App\Domains\Academic\Domain\Enums\EntityStatus;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicProgram extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Academic\AcademicProgramFactory();
    }

    protected $fillable = [
        'school_id',
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

    public function educationLevel(): BelongsTo
    {
        return $this->belongsTo(EducationLevel::class, 'education_level_id');
    }

    public function streams(): HasMany
    {
        return $this->hasMany(Stream::class, 'academic_program_id');
    }

    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'academic_program_id');
    }
}
