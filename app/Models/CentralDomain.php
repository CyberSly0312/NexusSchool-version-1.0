<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\School\Models\School;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CentralDomain extends Model
{
    protected $table = 'domains';

    protected $fillable = [
        'domain',
        'tenant_id',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(School::class, 'tenant_id');
    }
}
