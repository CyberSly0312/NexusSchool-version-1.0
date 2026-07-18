<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\School\Models\School;
use Illuminate\Support\Str;

class AiPromptTemplate extends Model
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
        'description',
        'system_prompt',
        'user_prompt_template',
        'variables',
        'status',
        'version',
        'target_model',
        'temperature',
        'max_tokens',
        'top_p',
        'metadata',
        'created_by',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (AiPromptTemplate $model) {
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
            'variables' => 'array',
            'version' => 'integer',
            'temperature' => 'float',
            'max_tokens' => 'integer',
            'top_p' => 'float',
            'metadata' => 'array',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForSchool($query, string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function render(array $variables = []): string
    {
        $template = $this->user_prompt_template;

        foreach ($variables as $key => $value) {
            $template = str_replace('{{'.$key.'}}', (string) $value, $template);
        }

        return $template;
    }
}
