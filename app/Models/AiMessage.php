<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\School\Models\School;
use Illuminate\Support\Str;

class AiMessage extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'ai_conversation_id',
        'role',
        'content',
        'input_tokens',
        'output_tokens',
        'model',
        'finish_reason',
        'metadata',
        'is_archived',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (AiMessage $model) {
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
            'metadata' => 'array',
            'is_archived' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AiConversation::class, 'ai_conversation_id');
    }

    public function scopeForSchool($query, string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeForConversation($query, string $conversationId)
    {
        return $query->where('ai_conversation_id', $conversationId);
    }

    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    public function totalTokens(): int
    {
        return $this->input_tokens + $this->output_tokens;
    }
}
