<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\School\Models\School;
use Illuminate\Support\Str;

class AiKnowledgeSource extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'name',
        'slug',
        'type',
        'description',
        'status',
        'source_path',
        'source_type',
        'content_type',
        'content_size',
        'content_hash',
        'version',
        'chunk_count',
        'total_tokens',
        'configuration',
        'metadata',
        'indexed_at',
        'created_by',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (AiKnowledgeSource $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'content_size' => 'integer',
            'version' => 'integer',
            'chunk_count' => 'integer',
            'total_tokens' => 'integer',
            'configuration' => 'array',
            'metadata' => 'array',
            'indexed_at' => 'datetime',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'indexed');
    }

    public function scopeForSchool($query, string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
