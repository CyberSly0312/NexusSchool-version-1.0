<?php

declare(strict_types=1);

namespace App\Domains\Department\Events;

use App\Models\Department;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DepartmentUpdated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Department $department,
    ) {
    }
}
