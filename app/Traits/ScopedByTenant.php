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

        // Fail closed: a tenant-owned query must never silently return every
        // school's records when no tenant is initialized. Legitimate
        // cross-tenant access must go through a dedicated central resource that
        // does not carry this trait (e.g. CentralUserResource).
        if ($tenantId === null) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('school_id', $tenantId);
    }
}
