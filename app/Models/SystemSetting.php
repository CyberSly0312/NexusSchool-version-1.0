<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\School\Models\School;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class SystemSetting extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'group',
        'key',
        'value',
        'type',
        'is_encrypted',
        'is_readonly',
        'is_system',
        'description',
        'metadata',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (SystemSetting $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_encrypted' => 'boolean',
            'is_readonly' => 'boolean',
            'is_system' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function getDecryptedValue()
    {
        if ($this->is_encrypted && $this->value) {
            return Crypt::decryptString($this->value);
        }

        return $this->attributes['value'] ?? null;
    }

    public function setEncryptedValue(string $value): void
    {
        $this->update([
            'value' => Crypt::encryptString($value),
            'is_encrypted' => true,
        ]);
    }

    public function scopeForSchool($query, ?string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeByGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    public function scopeGlobal($query)
    {
        return $query->whereNull('school_id');
    }
}
