<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\Communication\Enums\MessageStatus;
use App\Domains\Communication\Enums\MessageType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Message extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'sender_id',
        'subject',
        'body',
        'type',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'status' => MessageStatus::class,
        'type' => MessageType::class,
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

    public function recipients(): HasMany
    {
        return $this->hasMany(MessageRecipient::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeDirect($query)
    {
        return $query->where('type', MessageType::Direct);
    }

    public function scopeBroadcast($query)
    {
        return $query->where('type', MessageType::Broadcast);
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isDirect(): bool
    {
        return $this->type === MessageType::Direct;
    }

    public function isBroadcast(): bool
    {
        return $this->type === MessageType::Broadcast;
    }

    public function isDraft(): bool
    {
        return $this->status === MessageStatus::Draft;
    }

    public function isSent(): bool
    {
        return $this->status === MessageStatus::Sent;
    }
}
