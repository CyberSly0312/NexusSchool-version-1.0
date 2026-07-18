<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\Communication\Enums\NotificationChannel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CommunicationChannel extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'name',
        'channel_type',
        'is_active',
        'config',
        'from_address',
        'from_name',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
        'channel_type' => NotificationChannel::class,
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

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isEmail(): bool
    {
        return $this->channel_type === NotificationChannel::Email;
    }

    public function isSMS(): bool
    {
        return $this->channel_type === NotificationChannel::SMS;
    }

    public function isWhatsApp(): bool
    {
        return $this->channel_type === NotificationChannel::WhatsApp;
    }

    public function isPush(): bool
    {
        return $this->channel_type === NotificationChannel::Push;
    }

    public function isInApp(): bool
    {
        return $this->channel_type === NotificationChannel::InApp;
    }
}
