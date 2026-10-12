<?php

declare(strict_types=1);

namespace App\Domains\RecordsLifecycle\Infrastructure\Models;

use App\Contracts\SchoolOwned;
use App\Domains\RecordsLifecycle\Domain\Enums\RecordLifecycleState;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class RecordLifecycle extends Model implements SchoolOwned
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'record_lifecycles';

    protected $fillable = [
        'uuid',
        'school_id',
        'source_domain',
        'source_type',
        'source_id',
        'source_version',
        'lifecycle_state',
        'academic_context',
        'created_by',
        'created_at',
        'published_at',
        'archived_at',
        'origin_event',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'lifecycle_state' => RecordLifecycleState::class,
            'academic_context' => 'array',
            'published_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    public function canTransitionTo(RecordLifecycleState $to): bool
    {
        return $to->allowsTransitionFrom($this->lifecycle_state ?? RecordLifecycleState::Registered);
    }

    public function assertCanTransitionTo(RecordLifecycleState $to): void
    {
        if (! $this->canTransitionTo($to)) {
            throw new \InvalidArgumentException('Invalid lifecycle transition');
        }
    }
}
