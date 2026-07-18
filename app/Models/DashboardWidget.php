<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\Analytics\Enums\WidgetType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\School\Models\School;
use Illuminate\Support\Str;

class DashboardWidget extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'dashboard_widgets';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'school_id', 'dashboard_id', 'title', 'widget_type',
        'data_source', 'config', 'position', 'is_active',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (DashboardWidget $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'widget_type' => WidgetType::class,
            'config' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function dashboard(): BelongsTo
    {
        return $this->belongsTo(Dashboard::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDashboard($query, string $dashboardId)
    {
        return $query->where('dashboard_id', $dashboardId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }
}
