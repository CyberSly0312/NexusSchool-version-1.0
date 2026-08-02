<?php

declare(strict_types=1);

namespace App\Domains\Academic\Infrastructure\Models;

use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Assigns a subject to a class together with its grading weight.
 */
class Coefficient extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'school_class_id',
        'academic_subject_id',
        'coefficient',
        'is_optional',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'coefficient' => 'decimal:2',
            'is_optional' => 'boolean',
        ];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(AcademicSubject::class, 'academic_subject_id');
    }

    public function value(): float
    {
        return (float) $this->coefficient;
    }
}
