<?php

namespace App\Domains\Program\Events;

use App\Models\Program;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProgramCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Program $program,
    ) {}
}
