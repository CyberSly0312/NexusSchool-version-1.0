<?php

namespace App\Domains\Guardian\Events;

use App\Models\Guardian;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GuardianCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Guardian $guardian,
    ) {}
}
