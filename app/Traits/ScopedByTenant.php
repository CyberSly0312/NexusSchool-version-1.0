<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ScopedByTenant
{
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $tenantId = tenant()?->id;
        if ($tenantId) {
            $query->where('school_id', $tenantId);
        }

        return $query;
    }
}
