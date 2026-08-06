<?php

declare(strict_types=1);

namespace App\Domains\Staff\Infrastructure\Models;

use App\Domains\Staff\Domain\Enums\QualificationLevel;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Academic qualification of a staff member.
 */
class Qualification extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'qualifications';

    protected $fillable = [
        'uuid',
        'school_id',
        'staff_id',
        'qualification_level',
        'field_of_study',
        'institution',
        'qualification_date',
        'grade',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'qualification_level' => QualificationLevel::class,
            'qualification_date' => 'date',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Staff\QualificationFactory();
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
