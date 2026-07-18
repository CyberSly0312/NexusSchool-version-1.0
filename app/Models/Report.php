<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\Analytics\Enums\ReportFormat;
use App\Domains\Analytics\Enums\ReportStatus;
use App\Domains\Analytics\Enums\ScheduleFrequency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\School\Models\School;
use Illuminate\Support\Str;

class Report extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'school_id', 'name', 'slug', 'description', 'type',
        'format', 'status', 'config', 'scheduled_at',
        'scheduled_frequency', 'last_generated_at', 'generated_by',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Report $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'format' => ReportFormat::class,
            'status' => ReportStatus::class,
            'scheduled_frequency' => ScheduleFrequency::class,
            'config' => 'array',
            'scheduled_at' => 'datetime',
            'last_generated_at' => 'datetime',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', ReportStatus::Failed);
    }

    public function scopeForSchool($query, string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', ReportStatus::Scheduled)
            ->where('scheduled_at', '<=', now());
    }
}
