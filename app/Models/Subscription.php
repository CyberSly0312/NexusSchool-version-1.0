<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\School\Models\School;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Subscription extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'plan_id',
        'starts_at',
        'expires_at',
        'status',
        'auto_renew',
        'payment_amount',
        'payment_method',
        'payment_reference',
        'notes',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Subscription $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'starts_at' => 'date',
            'expires_at' => 'date',
            'payment_amount' => 'decimal:2',
            'auto_renew' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
