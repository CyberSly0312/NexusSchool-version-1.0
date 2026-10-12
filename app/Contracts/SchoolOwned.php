<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * A model that is owned by exactly one school (tenant).
 *
 * Implementing classes must have a non-nullable school_id attribute
 * referencing the owning tenant (see {@see \App\Traits\HasSchoolId}). This
 * contract expresses model ownership, not the active tenant context of a
 * request.
 */
interface SchoolOwned
{
    /**
     * The id of the school (tenant) that owns this model.
     */
    public function getSchoolId(): string;
}
