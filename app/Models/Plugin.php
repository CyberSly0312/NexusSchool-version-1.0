<?php
declare(strict_types=1);

namespace App\Models;

use App\Domains\Plugin\Enums\PluginStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Plugin extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'version',
        'author',
        'license',
        'status',
        'path',
        'settings',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => PluginStatus::class,
            'settings' => 'array',
            'metadata' => 'array',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Plugin $model): void {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    public function scopeEnabled($query)
    {
        return $query->where('status', PluginStatus::Enabled);
    }

    public function scopeInstalled($query)
    {
        return $query->where('status', PluginStatus::Installed);
    }

    public function isEnabled(): bool
    {
        return $this->status === PluginStatus::Enabled;
    }
}
