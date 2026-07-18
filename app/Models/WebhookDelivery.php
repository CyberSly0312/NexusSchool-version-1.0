<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\School\Models\School;
use Illuminate\Support\Str;

class WebhookDelivery extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'webhook_id', 'school_id', 'event', 'payload', 'response_status',
        'response_body', 'success', 'delivered_at', 'retry_count',
        'max_retries', 'next_retry_at',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (WebhookDelivery $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'response_status' => 'integer',
            'success' => 'boolean',
            'delivered_at' => 'datetime',
            'next_retry_at' => 'datetime',
            'retry_count' => 'integer',
            'max_retries' => 'integer',
        ];
    }

    public function webhook(): BelongsTo
    {
        return $this->belongsTo(Webhook::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function scopeForSchool($query, string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    public function scopePendingRetry($query)
    {
        return $query->where('success', false)
            ->where('retry_count', '<', \DB::raw('max_retries'))
            ->where(function ($q) {
                $q->whereNull('next_retry_at')
                    ->orWhere('next_retry_at', '<=', now());
            });
    }
}
