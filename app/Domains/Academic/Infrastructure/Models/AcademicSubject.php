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

class AcademicSubject extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Academic\AcademicSubjectFactory();
    }

    protected $fillable = [
        'school_id',
        'subject_group_id',
        'name',
        'code',
        'abbreviation',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => EntityStatus::class,
        ];
    }

    public function subjectGroup(): BelongsTo
    {
        return $this->belongsTo(SubjectGroup::class, 'subject_group_id');
    }

    public function coefficients(): HasMany
    {
        return $this->hasMany(Coefficient::class, 'academic_subject_id');
    }
}
