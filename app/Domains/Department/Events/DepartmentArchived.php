<?php

namespace App\Domains\Department\Events;

use App\Models\Department;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DepartmentArchived
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Department $department,
    ) {}
}
