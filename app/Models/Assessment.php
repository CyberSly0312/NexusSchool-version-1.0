<?php

namespace App\Models;

use App\Domains\School\Models\School;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Assessment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'school_id',
        'academic_year_id',
        'academic_term_id',
        'user_id',
        'subject_id',
        'academic_class_id',
        'assessment_code',
        'title',
        'assessment_type',
        'assessment_category',
        'maximum_score',
        'passing_score',
        'weight',
        'assessment_date',
        'publish_date',
        'submission_deadline',
        'status',
        'is_published',
        'is_locked',
        'notes',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Assessment $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'maximum_score' => 'decimal:2',
            'passing_score' => 'decimal:2',
            'weight' => 'decimal:2',
            'assessment_date' => 'date',
            'publish_date' => 'date',
            'submission_deadline' => 'date',
            'is_published' => 'boolean',
            'is_locked' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function academicClass(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class);
    }

    public function assessmentResults(): HasMany
    {
        return $this->hasMany(AssessmentResult::class);
    }
}
