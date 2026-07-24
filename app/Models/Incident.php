<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\Incident\Enums\IncidentSeverity;
use App\Domains\Incident\Enums\IncidentStatus;
use App\Domains\School\Models\School;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Incident extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'title',
        'description',
        'severity',
        'status',
        'incident_date',
        'location',
        'witnesses',
        'actions_taken',
        'follow_up',
        'reported_by',
        'assigned_to',
        'resolved_at',
        'resolution_notes',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Incident $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'severity' => IncidentSeverity::class,
            'status' => IncidentStatus::class,
            'incident_date' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'incident_student');
    }
}
