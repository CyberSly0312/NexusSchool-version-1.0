<?php

declare(strict_types=1);

namespace App\Domains\Academic\Infrastructure\Models;

use App\Domains\Academic\Domain\Enums\EntityStatus;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A section/group inside a class (e.g. "A", "B").
 */
class ClassSection extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'school_class_id',
        'name',
        'code',
        'capacity',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => EntityStatus::class,
            'capacity' => 'integer',
        ];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }
}
