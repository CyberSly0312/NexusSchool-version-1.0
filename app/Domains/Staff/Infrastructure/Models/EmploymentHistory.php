<?php

declare(strict_types=1);

namespace App\Domains\Staff\Infrastructure\Models;

use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Immutable append-only timeline of every employment event. Rows are never
 * updated or hard-deleted; the payload preserves the details of the change.
 */
class EmploymentHistory extends Model
{
    use HasFactory;
    use HasSchoolId;

    protected $table = 'employment_history';

    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'school_id',
        'employment_id',
        'staff_id',
        'event',
        'description',
        'payload',
        'user_id',
        'user_type',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'occurred_at' => 'datetime',
        ];
    }

    public function employment(): BelongsTo
    {
        return $this->belongsTo(Employment::class, 'employment_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
