<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\Document\Enums\DocumentType;
use App\Domains\School\Models\School;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DocumentTemplate extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'name',
        'type',
        'content',
        'header_html',
        'footer_html',
        'is_active',
        'is_default',
        'settings',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (DocumentTemplate $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'type' => DocumentType::class,
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'settings' => 'array',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function generatedDocuments()
    {
        return $this->hasMany(GeneratedDocument::class, 'template_id');
    }
}
