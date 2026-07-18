<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\Communication\Enums\NotificationChannel;
use App\Domains\Communication\Enums\NotificationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Notification extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'sender_id',
        'template_id',
        'type',
        'title',
        'body',
        'data',
        'status',
        'channel',
        'scheduled_at',
        'sent_at',
        'delivered_at',
        'read_at',
        'retry_count',
        'max_retries',
        'last_error',
    ];

    protected $casts = [
        'data' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'retry_count' => 'integer',
        'max_retries' => 'integer',
        'status' => NotificationStatus::class,
        'channel' => NotificationChannel::class,
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function school(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'school_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(NotificationTemplate::class, 'template_id');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(NotificationDelivery::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isPending(): bool
    {
        return $this->status === NotificationStatus::Pending;
    }

    public function isQueued(): bool
    {
        return $this->status === NotificationStatus::Queued;
    }

    public function isSent(): bool
    {
        return $this->status === NotificationStatus::Sent;
    }

    public function isDelivered(): bool
    {
        return $this->status === NotificationStatus::Delivered;
    }

    public function isFailed(): bool
    {
        return $this->status === NotificationStatus::Failed;
    }

    public function isCancelled(): bool
    {
        return $this->status === NotificationStatus::Cancelled;
    }

    public function isScheduled(): bool
    {
        return $this->scheduled_at !== null && $this->scheduled_at->isFuture();
    }

    public function canRetry(): bool
    {
        return $this->isFailed() && $this->retry_count < $this->max_retries;
    }
}
