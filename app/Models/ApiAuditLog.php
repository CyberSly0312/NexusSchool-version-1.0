<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\School\Models\School;
use Illuminate\Support\Str;

class ApiAuditLog extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'api_client_id', 'school_id', 'user_id', 'api_token_id',
        'method', 'path', 'status_code', 'ip_address', 'user_agent',
        'request_body', 'response_time_ms',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (ApiAuditLog $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'request_body' => 'array',
            'status_code' => 'integer',
            'response_time_ms' => 'integer',
        ];
    }

    public function apiClient(): BelongsTo
    {
        return $this->belongsTo(ApiClient::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function apiToken(): BelongsTo
    {
        return $this->belongsTo(ApiToken::class);
    }

    public function scopeForSchool($query, string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeForClient($query, string $clientId)
    {
        return $query->where('api_client_id', $clientId);
    }
}
