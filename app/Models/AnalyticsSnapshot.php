<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\School\Models\School;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AnalyticsSnapshot extends Model
{
    use HasFactory;

    protected $table = 'analytics_snapshots';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'school_id', 'snapshot_type', 'data', 'period_start',
        'period_end', 'generated_at', 'is_immutable',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (AnalyticsSnapshot $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'period_start' => 'datetime',
            'period_end' => 'datetime',
            'generated_at' => 'datetime',
            'is_immutable' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function scopeForSchool($query, string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('snapshot_type', $type);
    }

    public function scopeForPeriod($query, string $start, string $end)
    {
        return $query->where('period_start', '>=', Carbon::parse($start)->startOfDay()->toDateTimeString())
            ->where('period_end', '<=', Carbon::parse($end)->endOfDay()->toDateTimeString());
    }

    public function scopeImmutable($query)
    {
        return $query->where('is_immutable', true);
    }
}
