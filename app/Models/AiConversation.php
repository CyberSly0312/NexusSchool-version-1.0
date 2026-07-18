<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\School\Models\School;
use Illuminate\Support\Str;

class AiConversation extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'ai_provider_id',
        'user_id',
        'title',
        'status',
        'context_type',
        'context_id',
        'model',
        'system_prompt',
        'summary',
        'message_count',
        'total_input_tokens',
        'total_output_tokens',
        'context_window_used',
        'context_window_limit',
        'metadata',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (AiConversation $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'message_count' => 'integer',
            'total_input_tokens' => 'integer',
            'total_output_tokens' => 'integer',
            'context_window_used' => 'integer',
            'context_window_limit' => 'integer',
            'metadata' => 'array',
            'status' => \App\Domains\Ai\Enums\AiConversationStatus::class,
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AiMessage::class, 'ai_conversation_id');
    }

    public function usageLogs(): HasMany
    {
        return $this->hasMany(AiUsageLog::class, 'ai_conversation_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForSchool($query, string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForContext($query, string $contextType, string $contextId)
    {
        return $query->where('context_type', $contextType)->where('context_id', $contextId);
    }

    public function totalTokens(): int
    {
        return $this->total_input_tokens + $this->total_output_tokens;
    }
}
