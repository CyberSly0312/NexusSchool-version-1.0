<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campus extends Model
{
    use HasFactory, HasSchoolId, SoftDeletes;

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'address',
        'city',
        'phone',
        'email',
        'description',
        'is_main',
        'display_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_main' => 'boolean',
            'display_order' => 'integer',
        ];
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function academicClasses()
    {
        return $this->hasMany(AcademicClass::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
