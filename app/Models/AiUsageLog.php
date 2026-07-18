<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\School\Models\School;
use Illuminate\Support\Str;

class AiUsageLog extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'ai_provider_id',
        'ai_conversation_id',
        'user_id',
        'action',
        'model',
        'input_tokens',
        'output_tokens',
        'total_tokens',
        'estimated_cost',
        'latency_ms',
        'was_cached',
        'is_billable',
        'status',
        'error_message',
        'metadata',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (AiUsageLog $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'input_tokens' => 'integer',
            'output_tokens' => 'integer',
            'total_tokens' => 'integer',
            'estimated_cost' => 'float',
            'latency_ms' => 'integer',
            'was_cached' => 'boolean',
            'is_billable' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(AiProvider::class, 'ai_provider_id');
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AiConversation::class, 'ai_conversation_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForSchool($query, string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeForProvider($query, string $providerId)
    {
        return $query->where('ai_provider_id', $providerId);
    }

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeBillable($query)
    {
        return $query->where('is_billable', true);
    }

    public function scopeForPeriod($query, string $from, string $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }
}
