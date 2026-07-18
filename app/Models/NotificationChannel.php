<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\School\Models\School;
use Illuminate\Support\Str;

class NotificationChannel extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'notification_channels';

    protected $fillable = [
        'school_id',
        'name',
        'type',
        'is_active',
        'configuration',
        'conditions',
        'description',
        'total_sent',
        'total_failed',
        'last_used_at',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (NotificationChannel $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'configuration' => 'array',
            'conditions' => 'array',
            'total_sent' => 'integer',
            'total_failed' => 'integer',
            'last_used_at' => 'datetime',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForSchool($query, ?string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }
}
