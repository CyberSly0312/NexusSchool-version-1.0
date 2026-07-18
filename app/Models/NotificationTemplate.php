<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\Communication\Enums\NotificationChannel;
use App\Domains\Communication\Enums\TemplateCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class NotificationTemplate extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'name',
        'slug',
        'category',
        'channel',
        'subject',
        'body',
        'variables',
        'is_active',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
        'category' => TemplateCategory::class,
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

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function render(array $variables = []): string
    {
        $body = $this->body;

        foreach ($variables as $key => $value) {
            $body = str_replace("{{ {$key} }}", (string) $value, $body);
        }

        return $body;
    }

    public function renderSubject(array $variables = []): ?string
    {
        if ($this->subject === null) {
            return null;
        }

        $subject = $this->subject;

        foreach ($variables as $key => $value) {
            $subject = str_replace("{{ {$key} }}", (string) $value, $subject);
        }

        return $subject;
    }

    public function getAvailableVariables(): array
    {
        return $this->variables ?? [];
    }
}
