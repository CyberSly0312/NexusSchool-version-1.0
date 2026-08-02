<?php

declare(strict_types=1);

namespace App\Domains\Academic\Infrastructure\Models;

use App\Domains\Academic\Domain\Enums\EntityStatus;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubjectGroup extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'sort_order',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => EntityStatus::class,
            'sort_order' => 'integer',
        ];
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(AcademicSubject::class, 'subject_group_id');
    }
}
