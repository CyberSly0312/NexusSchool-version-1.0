<?php

namespace App\Domains\School\Events;

use App\Domains\School\Models\School;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SchoolCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly School $school,
    ) {}
}
