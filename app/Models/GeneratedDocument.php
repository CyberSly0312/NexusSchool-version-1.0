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

class GeneratedDocument extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'template_id',
        'type',
        'reference_number',
        'student_id',
        'title',
        'data',
        'file_path',
        'generated_by',
        'signed_by',
        'signed_at',
        'qr_code',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (GeneratedDocument $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
            if (empty($model->reference_number)) {
                $model->reference_number = strtoupper(Str::random(3) . '-' . date('Ymd') . '-' . Str::random(4));
            }
        });
    }

    protected function casts(): array
    {
        return [
            'type' => DocumentType::class,
            'data' => 'array',
            'signed_at' => 'datetime',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(DocumentTemplate::class, 'template_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function signer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signed_by');
    }
}
